<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentMethod;
use App\Models\Offer;
use App\Models\Request as ModelsRequest;
use App\Models\Wallet;
use App\Models\WalletHistory;

class WalletController extends Controller
{
    private function currentWallet(): ?Wallet
    {
        return Wallet::where('user_id', auth()->id())->first();
    }

    private function walletNotFoundResponse()
    {
        return response()->json([
            'status' => 0,
            'message' => 'Wallet not found',
        ]);
    }

    private function buildCustomerFragment(): string
    {
        $user = auth()->user();

        return sprintf(
            '"name": %s, "email": %s, "phone": %s, "street1": %s',
            json_encode($user->name ?? ''),
            json_encode($user->email ?? ''),
            json_encode($user->mobile ?? ''),
            json_encode($user->street_address ?? '')
        );
    }

    private function buildInvoiceItemsFragment(float $amount, int $walletId): string
    {
        $amount = number_format($amount, 2, '.', '');

        return sprintf(
            '{"sku": %s, "description": %s, "url": %s, "unit_cost": %s, "quantity": 1, "net_total": %s, "discount_rate": 0, "discount_amount": 0, "tax_rate": 0, "tax_total": 0, "total": %s}',
            json_encode((string) $walletId),
            json_encode('Recharge Amount'),
            json_encode(''),
            $amount,
            $amount,
            $amount
        );
    }

    private function apiResponse(array $payload, int $statusCode = 200)
    {
        return response()->json($payload, $statusCode);
    }

    public function charge_in(Request $req)
    {
        $data = $req->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|integer|exists:payment_methods,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $wallet = $this->currentWallet();

        if (!$wallet) {
            return $this->walletNotFoundResponse();
        }

        $paymentMethod = PaymentMethod::find($data['payment_method']);

        if (!$paymentMethod) {
            return $this->apiResponse([
                'status' => 0,
                'message' => 'Payment method not found.',
            ]);
        }

        if ($paymentMethod->slug !== 'click_pay') {
            return $this->apiResponse([
                'status' => 0,
                'message' => 'Unsupported payment method.',
            ]);
        }

        $walletHistory = WalletHistory::create([
            'wallet_id' => $wallet->id,
            'amount' => $data['amount'],
            'is_deposite' => 1,
            'description' => $data['note'] ?? null,
        ]);

        $urlData = [
            'amount' => $data['amount'],
            'wallet_id' => $wallet->id,
            'wh_id' => $walletHistory->id,
            'payment_method' => $paymentMethod->id,
        ];

        $walletPayload = [
            'description' => $data['note'] ?? '',
            'total' => $data['amount'],
            'customer' => $this->buildCustomerFragment(),
            'wallet_id' => $wallet->id,
            'invoice_items' => $this->buildInvoiceItemsFragment((float) $data['amount'], $wallet->id),
            'redirect_url' => url()->to('/charge_in/' . base64_encode(json_encode($urlData))),
            'profile_key' => $paymentMethod->public_key,
            'secret_key' => $paymentMethod->secret_key,
        ];

        $result = json_decode(Wallet::clickPay($walletPayload), true);
        $result = is_array($result) ? $result : [];
        $result['id'] = $wallet->id;

        if (isset($result['invoice_id'])) {
            $walletHistory->update([
                'invoice_id' => $result['invoice_id'],
            ]);

            return $this->apiResponse([
                'status' => 1,
                'data' => $result,
            ]);
        }

        return $this->apiResponse([
            'status' => 0,
            'message' => 'Transaction pending',
        ]);
    }

    public function getWalletSummary()
    {
        $wallet = $this->currentWallet();

        if (!$wallet) {
            return $this->apiResponse([
                'msg' => 'Wallet not found',
            ]);
        }

        $deposits = WalletHistory::where('wallet_id', $wallet->id)
            ->where('is_deposite', 1)
            ->sum('amount');

        $expenses = WalletHistory::where('wallet_id', $wallet->id)
            ->where('is_expanse', 1)
            ->sum('amount');

        $recentEntry = WalletHistory::where('wallet_id', $wallet->id)->where('is_deposite', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        return $this->apiResponse([
            'earnings' => $deposits,
            'withdral' => $expenses,
            'balance' => $wallet->amount,
            'current_earning' => $recentEntry,
        ]);
    }

    public function wallet()
    {
        $wallet = $this->currentWallet();

        if (!$wallet) {
            return $this->walletNotFoundResponse();
        }

        return $this->apiResponse([
            'status' => 1,
            'wallet' => $wallet->toArray(),
        ]);
    }

    public function walletTransfer(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|integer|exists:users,id|different:' . auth()->id(),
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
        ]);

        $senderWallet = $this->currentWallet();
        $receiverWallet = Wallet::where('user_id', $data['user_id'])->first();

        if (!$senderWallet || !$receiverWallet) {
            return $this->apiResponse([
                'status' => 0,
                'message' => 'Wallet not found.',
            ]);
        }

        if ((float) $senderWallet->amount < (float) $data['amount']) {
            return $this->apiResponse([
                'status' => 0,
                'message' => 'Wallet have not enough amount.',
            ]);
        }

        DB::beginTransaction();

        try {
            $senderWallet->update([
                'amount' => (float) $senderWallet->amount - (float) $data['amount'],
            ]);

            WalletHistory::create([
                'wallet_id' => $senderWallet->id,
                'amount' => $data['amount'],
                'is_expanse' => 1,
                'description' => $data['note'] ?? null,
            ]);

            $receiverWallet->update([
                'amount' => (float) $receiverWallet->amount + (float) $data['amount'],
            ]);

            WalletHistory::create([
                'wallet_id' => $receiverWallet->id,
                'amount' => $data['amount'],
                'is_deposite' => 1,
                'description' => $data['note'] ?? null,
            ]);

            DB::commit();

            return $this->apiResponse([
                'status' => 1,
                'message' => 'Amount transfer successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->apiResponse([
                'status' => 0,
                'message' => 'Amount transfer failed.',
            ]);
        }
    }

    public function walletHistory()
    {
        $wallet = $this->currentWallet();

        if (!$wallet) {
            return $this->walletNotFoundResponse();
        }

        $history = WalletHistory::where('wallet_id', $wallet->id)
            ->orderByDesc('id')
            ->get();

        return $this->apiResponse([
            'status' => 1,
            'history' => $history->toArray(),
        ]);
    }

    public function walletNotification()
    {
        $wallet = $this->currentWallet();

        if (!$wallet) {
            return $this->walletNotFoundResponse();
        }

        $history = WalletHistory::where('wallet_id', $wallet->id)
            ->where('is_read', 0)
            ->orderByDesc('id')
            ->get();

        return $this->apiResponse([
            'status' => 1,
            'history' => $history->toArray(),
        ]);
    }

    public function walletReadNotify($flag)
    {
        $wallet = $this->currentWallet();

        if (!$wallet) {
            return $this->walletNotFoundResponse();
        }

        if ($flag === 'all') {
            DB::table('wallet_histories')
                ->where('wallet_id', $wallet->id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            return $this->apiResponse([
                'status' => 1,
                'msg' => 'All notifications read successfully.',
            ]);
        }

        if (!is_numeric($flag)) {
            return $this->apiResponse([
                'status' => 0,
                'msg' => 'Invalid notification id.',
            ]);
        }

        $update = DB::table('wallet_histories')
            ->where('wallet_id', $wallet->id)
            ->where('id', (int) $flag)
            ->update(['is_read' => 1]);

        if ($update) {
            return $this->apiResponse([
                'status' => 1,
                'msg' => 'Notification read successfully.',
            ]);
        }

        $history = WalletHistory::where('wallet_id', $wallet->id)
            ->where('is_read', 0)
            ->orderByDesc('id')
            ->get();

        return $this->apiResponse([
            'status' => 1,
            'history' => $history->toArray(),
        ]);
    }

    public function recentTransactionHistory($limit)
    {
        $user = auth()->user();
        $limit = max(0, (int) $limit);

        if ($user->user_type == 2) {
            $wallet = $this->currentWallet();

            if (!$wallet) {
                return $this->walletNotFoundResponse();
            }

            $offerIds = Offer::where('user_id', $user->id)->pluck('id')->all();
            $requestsQuery = ModelsRequest::whereIn('offer_id', $offerIds)->where('status', 3);

            if ($limit > 0) {
                $requestsQuery->limit($limit);
            }

            $requests = $requestsQuery->get(['id', 'offer_id', 'parcel_address', 'receiver_address', 'amount']);

            $earning = WalletHistory::where('wallet_id', $wallet->id)->where('is_deposite', 1)->sum('amount');
            $withDraw = WalletHistory::where('wallet_id', $wallet->id)->where('is_expanse', 1)->sum('amount');

            return $this->apiResponse([
                'balance' => $wallet->amount,
                'total_earning' => $earning,
                'total_withdraw' => $withDraw,
                'transactions' => $requests,
            ]);
        }

        $requestsQuery = ModelsRequest::where('user_id', $user->id)->where('status', 3);

        if ($limit > 0) {
            $requestsQuery->limit($limit);
        }

        $requests = $requestsQuery->get(['id', 'offer_id', 'parcel_address', 'receiver_address', 'amount']);

        return $this->apiResponse([
            'transactions' => $requests,
        ]);
    }
}

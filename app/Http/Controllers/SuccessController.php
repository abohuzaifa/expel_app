<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\CardDetail;
use App\Models\Notification;
use App\Models\Request as ModelsRequest;
use App\Models\Wallet;
use App\Models\WalletHistory;

class SuccessController extends Controller
{
    //
    public function index($id, $offer_id)
    {
        // echo $id; exit;
        $id = base64_decode($id);
        $wdata['code'] = $id."|".generateRandomCode();
        $offer_id = base64_decode($offer_id);
            $data['status'] = 0;

        $request = ModelsRequest::find($id);
        $pm = PaymentMethod::where('slug', 'click_pay')->first();
        if($pm->slug == "click_pay"){
            $data['secret_key'] = $pm->secret_key;
            $data['invoice_id'] = $request->invoice_id;
            $status = Order::clickPayOrderStatus($data);
            $status = json_decode($status, true);
            $data['status'] = 0;
            if(isset($status['invoice_status']) && $status['invoice_status'] == "paid")
            {
                $data['status'] = 1;
                // print_r($status); exit;
                DB::table("requests")->where("id", "=", $request->id)->update([
                    "payment_status" => 1,
                    'offer_id' => $offer_id,
                    'status' => 1,
                    'code' => $wdata['code']
                ]);
                send_message($wdata, $request->receiver_mobile);
                $user = User::find($request->user_id);
                User::storeAppNotification(
                    $request->user_id,
                    'Your Request payment done successfully',
                    'request_page',
                    'account_updates'
                );
                // $data = [];
                $data['title'] = 'Payment';
                $data['body'] = 'Your request payment done successfully';
                $data['device_token'] = $user->device_token;
                $data['is_driver'] = 0;
                $data['request_id'] = $request->id;
                $data['user_id'] = $user->id;
                $data['setting_key'] = 'account_updates';
                User::sendNotification($data);
            }

        }
        return view('success',$data);
    }

    public function charge_in($id)
    {
        // echo $id; exit;
        $data = json_decode(base64_decode($id), true);
        // print_r($data); exit;
        $amount = $data['amount'];
        $wallet_id = $data['wallet_id'];
        $wh_id = $data['wh_id'];

        $wallet = WalletHistory::find($wh_id);
        $status = 0;
        $message = 'Payment verification failed.';
        if($wallet)
        {
            $pm = PaymentMethod::find($data['payment_method']);
            if($pm && $pm->slug == "click_pay"){
                $data['secret_key'] = $pm->secret_key;
                $data['invoice_id'] = $wallet->invoice_id;
                $paymentStatus = Order::clickPayOrderStatus($data);
                $paymentStatus = json_decode($paymentStatus, true);
                if(isset($paymentStatus['invoice_status']) && $paymentStatus['invoice_status'] == "paid")
                {
                    // print_r($status); exit;
                    $walletData = Wallet::find($wallet->wallet_id);
                    if($walletData)
                    {
                        $amount = $walletData->amount + $amount;
                        $flag = DB::table("wallets")->where("id", "=", $wallet_id )->update([
                            "amount" => $amount,
                        ]);
                        if($flag)
                        {
                            $status = 1;
                            $message = 'Payment verified successfully.';
                        }
                    } else {
                        $message = 'Wallet not found.';
                    }
                } else {
                    WalletHistory::where("id", "=", $wh_id)->update([
                        "status" => 0,
                    ]);
                    $message = 'Payment was not completed.';
                        }
            } else {
                $message = 'Unsupported payment method.';
                }
        }

        return view('charge_in', [
            'status' => $status,
            'message' => $message,
        ]);
    }
}

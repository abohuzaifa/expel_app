<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $data['perPage'] = 10;
        $data['wallets'] = Wallet::orderByDesc('id')->paginate($data['perPage']);
        return view('wallet.index', $data);
    }

    public function edit($id)
    {
        $wallet = Wallet::find($id);
        $user = User::find($wallet->user_id);
        return view('wallet.edit', compact('user', 'wallet'));
    }

    public function update($id, Request $request)
    {
        $amount = doubleval($request->input('amount'));
        $wallet = Wallet::find($id);
        $user = User::find($wallet->user_id);

        if ($request->has('recharge') && $request->input('recharge') == 1) {
            $newAmount = $amount + $wallet->amount;

            $wallet->update(['amount' => $newAmount]);

            WalletHistory::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'is_deposite' => 1,
                'description' => $user->name . ' ' . trans('lang.success_recharge_msg') . ' ' . $amount,
            ]);
        } elseif ($request->has('withdraw') && $request->input('withdraw') == 1) {
            $newAmount = $wallet->amount - $amount;

            $wallet->update(['amount' => $newAmount]);

            WalletHistory::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'is_expanse' => 1,
                'description' => $user->name . ' ' . trans('lang.success_withdraw_msg') . ' ' . $amount,
            ]);
        }

        return redirect()->route('wallet.index')->with('success', trans('lang.update_message'));
    }
}

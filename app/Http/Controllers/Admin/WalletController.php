<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\CardDetail;
use App\Models\Wallet;
use App\Models\WalletHistory;

class WalletController extends Controller
{
    //
    public function charge_in(Request $req)
    {
        $attrs = $req->validate([
            'amount' => "required",
            'payment_method' => "required"
        ]);
        $url_data['amount'] = $attrs['amount'];
        $wallet['description'] = $req->note;
        $user = auth()->user();
        $wallet['total'] = $attrs['amount'];
        $wallet['customer'] = '
            "name": "'.$user->name.'",
            "email": "'.$user->email.'",
            "phone": "'.$user->mobile.'",
            "street1": "'.$user->street_address.'"
            ';
        
        $wallet_data = DB::table("wallets")->where('user_id', $user->id)->get();
        // print_r($wallet_data); exit;
        if(count($wallet_data) > 0)
        {
            $wallet_data = $wallet_data[0];
            $wallet['wallet_id'] = $wallet_data->id;
            $url_data['wallet_id'] = $wallet_data->id;
        } else {
            return response([
                'status' => "0",
                "message" => "Wallet not exist"
            ]);
        }
        
        
        // echo $data['redirect_url']; exit;

        $wallet_history = WalletHistory::create([
            'wallet_id' => $wallet['wallet_id'],
            'amount' => $attrs['amount'],
            'is_deposite' => 1,
            'description' => $req->note,
        ]);
        
        if($wallet_history)
        {
            $wh_id = $wallet_history->id;
            $url_data['wh_id'] = $wh_id;
        } else {
            return response([
                'status' => "0",
                "message" => "Something went wrong"
            ]);
        }
        $wallet['invoice_items'] = '{
            "sku": "'.$wallet['wallet_id'].'",
            "description": "Recharge Amount",
            "url": "",
            "unit_cost": '.round($attrs['amount'], 2).',
            "quantity": 1,
            "net_total": '.round($attrs['amount'], 2).',
            "discount_rate": 0,
            "discount_amount": 0,
            "tax_rate": 0,
            "tax_total": 0,
            "total": '.round($attrs['amount'], 2).'
        }';
            $pm = PaymentMethod::find($attrs["payment_method"]);
            // print_r($pm); exit;
            if($pm->slug == "click_pay")
            {
                $url_data['payment_method'] = $pm->id;
                $wallet['redirect_url'] = url()->to('/charge_in/'.base64_encode(json_encode($url_data)));
                $wallet['profile_key'] = $pm->public_key;
                $wallet['secret_key'] = $pm->secret_key;
                $res = Wallet::clickPay($wallet);
                $res = json_decode($res, true);
                $res['id'] = $wallet_data->id;
                if(isset($res['invoice_id']))
                {
                    DB::table('wallet_histories')->where('id', $wh_id)->update([
                        'invoice_id' => $res['invoice_id']
                    ]);
                    return response([
                        'status' => "1",
                        "data" => $res
                    ]);
                } else {
                    return response([
                        'status' => "0",
                        "message" => "Transaction pending"
                    ]);
                }

            } else if($pm->slug == "COD"){
                // DB::table("orders")->where("id", "=", $order['id'])->update([
                //     'seller_id' =>$seller_id
                // ]);
                // DB::select("DELETE FROM carts WHERE user_id=".auth()->user()->id);
                // DB::commit();
                // return response([
                //     "status" => "1",
                //     "data" => $order
                // ]);

            }

    }

    public function wallet()
    {
        $wallet = DB::table('wallets')->where("user_id", auth()->user()->id)->get();
        return response([
            'status' => 1,
            "wallet" => json_decode(json_encode($wallet[0]), true)
        ]);
    }

    public function walletTransfer(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|int',
            'amount' => 'required',
        ]);
        DB::beginTransaction();
        $user_wallet = Wallet::where('user_id',auth()->user()->id)->first();
        $get_user_wallet = Wallet::where('user_id',$req->user_id)->first();
        // print_r($get_user_wallet); exit;
        if(doubleval($user_wallet->amount) < doubleval($data['amount']))
        {
            return response([
                'status' => 0,
                'message' =>  'Wallet have not enough amount.'
            ]);
        }
        $transfer_amount = doubleval($user_wallet->amount) - doubleval($data['amount']); //echo $transfer_amount; exit;
        $user_wault_update = DB::table('wallets')->where('user_id', auth()->user()->id)->update([
            'amount' => $transfer_amount
        ]); //echo $user_wault_update; exit;
        if($user_wault_update)
        {
            $wallet_history1 = WalletHistory::create([
                'wallet_id' => $user_wallet['id'],
                'amount' => $data['amount'],
                'is_expanse' => 1,
                'description' => $req->note,
            ]);

            $in_amount_total = $data['amount'] + $get_user_wallet->amount;
            $get_user_wault_update = DB::table('wallets')->where('user_id', $data['user_id'])->update([
                'amount' => $in_amount_total
            ]); //echo $get_user_wault_update; exit;

            if($get_user_wault_update)
            {
                $wallet_history2 = WalletHistory::create([
                    'wallet_id' => $get_user_wallet['id'],
                    'amount' => $data['amount'],
                    'is_deposite' => 1,
                    'description' => $req->note,
                ]);
                DB::commit();
                return response([
                    'status' => 1,
                    'message' => 'Amount transfer successfully'
                ]);
            } else {
                return response([
                    'status' => 0,
                    'message' => 'Amount transfer failed.'
                ]);
            }
        } else {
            return response([
                'status' => 0,
                'message' => 'Amount transfer failed.'
            ]);
        }
    }

    public function walletHistory()
    {
        $wallet = Wallet::where('user_id', auth()->user()->id)->first();
        $History = WalletHistory::where('wallet_id', $wallet->id)->get();

        return response([
            'status' => 1,
            'history' => json_decode(json_encode($History), true)
        ]);
    }

    public function walletNotification()
    {
        $wallet = Wallet::where('user_id', auth()->user()->id)->first();
        $History = WalletHistory::where('wallet_id', $wallet->id)->where('is_read', 0)->get();

        return response([
            'status' => 1,
            'history' => json_decode(json_encode($History), true)
        ]);
    }

    public function walletReadNotify($flag)
    {
        if($flag == 'all')
        {
            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            $update = DB::table('wallet_histories')->where('wallet_id', $wallet->id)->update(['is_read' => 1]);
            if($update)
            {
                return response([
                    'status' => 1,
                    'msg' => 'All notifications read successfully.'
                ]);
            }
        } else {
            $update = DB::table('wallet_histories')->where('id', $flag)->update(['is_read' => 1]);
            if($update)
            {
                return response([
                    'status' => 1,
                    'msg' => 'Notification read successfully.'
                ]);
            }
        }
        $wallet = Wallet::where('user_id', auth()->user()->id)->first();
        $History = WalletHistory::where('wallet_id', $wallet->id)->where('is_read', 0)->get();

        return response([
            'status' => 1,
            'history' => json_decode(json_encode($History), true)
        ]);
    }
}

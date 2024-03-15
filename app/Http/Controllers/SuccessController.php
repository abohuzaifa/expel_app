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
use App\Models\Wallet;
use App\Models\WalletHistory;

class SuccessController extends Controller
{
    //
    public function index($id)
    {
        // echo $id; exit;
        $id = base64_decode($id);
        $order = Order::find($id);
        $pm = PaymentMethod::find($order->payment_method);
        if($pm->slug == "click_pay"){
            $data['secret_key'] = $pm->secret_key;
            $data['invoice_id'] = $order->invoice_id;
            $status = Order::clickPayOrderStatus($data);
            $status = json_decode($status, true);
            if(isset($status['invoice_status']) && $status['invoice_status'] == "paid")
            {
                // print_r($status); exit;
                DB::table("orders")->where("id", "=", $order->id)->update([
                    "order_status" => 2,
                    "paid" => $order->total,
                    "due" => 0
                ]);
            }

        }
        return view('success');
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
        if($wallet)
        {
            $pm = PaymentMethod::find($data['payment_method']);
            if($pm->slug == "click_pay"){
                $data['secret_key'] = $pm->secret_key;
                $data['invoice_id'] = $wallet->invoice_id;
                $status = Order::clickPayOrderStatus($data);
                $status = json_decode($status, true);
                if(isset($status['invoice_status']) && $status['invoice_status'] == "paid")
                {
                    // print_r($status); exit;
                    DB::table("wallets")->where("id", "=", $wallet_id )->update([
                        "amount" => $amount,
                    ]);
                }
    
            }
            return view('charge_in');

        }
    }
}

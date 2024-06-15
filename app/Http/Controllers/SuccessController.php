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
                $notification = new Notification();
                $notification->user_id = $request->user_id; // Assuming the user is authenticated
                $notification->message = 'Your Request payment done successfully';
                $notification->page = 'request_page';
                $notification->save();
                // $data = [];
                $data['title'] = 'Payment';
                $data['body'] = 'Your request payment done successfully';
                $data['device_token'] = $user->device_token;
                $data['is_driver'] = 0;
                $data['request_id'] = $request->id;
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

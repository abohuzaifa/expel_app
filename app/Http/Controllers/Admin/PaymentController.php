<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\PaymentMethod;

class PaymentController extends Controller
{
    //
    public function create(Request $request)
    {
        $attrs = $request->validate([
            "name"=> "required|string|unique:payment_methods",
            "name_ar"=> "required|string",
            "slug"=> "required|string|unique:payment_methods",
        ]);
       
        $paymetMethod = PaymentMethod::create([
            "name"=> $attrs["name"],
            "name_ar"=> $attrs["name_ar"],
            "status"=> 1,
            "public_key"=> $request->public_key,
            "secret_key"=> $request->secret_key,
            "created_by"=> auth()->user()->id,
            "slug"=> $request->slug,
        ]);

        if($paymetMethod)
        {
            return response([
                "status" => "1",
                "payment_method" => json_decode(json_encode($paymetMethod), true),
            ]);
        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong"
            ]);
        }
    }

    public function list(){
        $list = DB::table("payment_methods")->where("status", 1)->get();

        if($list)
        {
            return response([
                "status"=> "1",
                "list"=> json_decode(json_encode($list), true)
            ]);
        } else {
            return response([
                "status"=> "0",
                "message"=> "Payment list not found"
            ]);
        }
    }
}

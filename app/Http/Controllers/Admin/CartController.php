<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;

class CartController extends Controller
{
    //
    public function cartOrder(Request $request)
    {
        $attrs = $request->validate([
            "order_id"=> "required|int",
        ]);

        $data = Cart::where("order_id", $attrs["order_id"])->where("user_id", auth()->user()->id)->first();
        if (empty($data)) {
            $cart = Cart::create([
                "order_id"=> $attrs["order_id"],
                "is_manual"=> 1,
                "user_id"=> auth()->user()->id,
            ]);
            if($cart)
            {
                return response([
                    'status' => 'success',
                    'cart' => $cart
                ], 200);
            } else {
                return response([
                    'status'=> 'error',
                    'message'=> "Something went wrong",
                ],500);
            }
        } else {
            return response([
                "status"=> "error",
                "message"=> "This is already in cart"
                ],200);
        }

    }
    public function cart(Request $request)
    {
        $attrs = $request->validate([
            "product_id"=> "required|int",
            "quantity"=> "required|int",
        ]);

        $data = Cart::where("product_id", $attrs["product_id"])->where("user_id", auth()->user()->id)->first();
        if (empty($data)) {
            $cart = Cart::create([
                "product_id"=> $attrs["product_id"],
                "quantity"=> $attrs["quantity"],
                "user_id"=> auth()->user()->id,
            ]);
            if($cart)
            {
                return response([
                    'status' => 'success',
                    'cart' => $cart
                ], 200);
            } else {
                return response([
                    'status'=> 'error',
                    'message'=> "Something went wrong",
                ],500);
            }
        } else {
            return response([
                "status"=> "error",
                "message"=> "This is already in cart"
                ],200);
        }

    }
    public function cartView($return = 0)
    {
        $cart = DB::select("SELECT * FROM carts WHERE user_id=:uid", [':uid' => auth()->user()->id]);
        $cart = json_decode(json_encode($cart), true);
        
        $cart_data = [];
        $total_tax = $total_discount = $total = 0;
        if(is_array($cart) && count($cart) > 0) {
            foreach ($cart as $k => $v) {
                if(isset($v['order_id']) && $v['order_id'] > 0)
                {
                    $product = Order::find($v['order_id']);
                    $total += $product->total;
                } else {
                    $qty = $v['quantity'];
                    $product = DB::table('products')->where('id', $v['product_id'])->first();
                    $product->quantity = $qty;
    
                    $product->total_tax = round($product->tax*$qty, 2);
                    $total_tax += $product->total_tax;
    
                    $product->total_discount = round($product->discount*$qty, 2);
                    $total_discount += $product->total_discount;
    
                    $product->total_price = round($product->price*$qty,2);
    
                    $product->net_total = round(($product->total_price + $product->total_tax)-$product->total_discount, 2);
                    $total += $product->net_total;
    
                    $product->image_base_url = asset('images/');
                    
                }
                array_push($cart_data, $product);
            }
            $data = [
                'total' => $total,
                'total_tax' => $total_tax,
                'total_dicount' => $total_discount,
                'cart' => json_decode(json_encode($cart_data), true)
            ];
            if($return == 1)
            {
                return $data;
                exit();
            }
            if(count($cart_data) > 0) {
                return response([
                    'status'=> 'success',
                    'data'=> $data,
                ],200);
            } else {
                return response([
                    'status'=> 'error',
                    'message'=> 'No product found.'
                ],200);
            }
        } else {
            if($return == 1)
            {
                return [];
                exit();
            }
            return response([
                'status'=> 'success',
                'cart' => $cart_data
            ],200);
        }
    }

    public function updateQunatity($id, Request $request)
    {
        $attrs = $request->validate([
            'quantity'=> 'required|int',
        ]);

        $cart = DB::update("UPDATE carts SET quantity=:qty WHERE product_id=:pid AND user_id=:uid", [':qty' => $attrs['quantity'], ':pid' => $id, ':uid' => auth()->user()->id]);
        if($cart)
        {
            $product = DB::table('products')->where('id', $id)->first();
            $qty = $attrs['quantity'];
            $product->quantity = $attrs['quantity'];
            $product->total_tax = round($product->tax*$qty, 2);
            $product->total_discount = round($product->discount*$qty, 2);

            $product->total_price = round($product->price*$qty,2);

            $product->net_total = round(($product->total_price + $product->total_tax)-$product->total_discount, 2);
            
            $product->image_base_url = asset('images/');
            return response([
                'status'=> 'success',
                'item'=> json_decode(json_encode($product), true),
            ],200);
        } else {
            return response([
                'status'=> 'error',
                'message'=> 'Something went wrong'
            ],200);
        }
    }

    public function removeItem($id)
    {
        $cart = DB::table('carts')->where('product_id', $id)->where('user_id', auth()->user()->id)->first();
        if($cart)
        {
            $cart = Cart::find($cart->id);
            if($cart->delete())
            {
                return response([
                    'status'=> 'success',
                    'message'=> 'Item remove successfully'
                ],200);
            } else {
                return response([
                    'status'=> 'error',
                    'message'=> 'Something went wrong'
                ],200);
            }
        } else {
            return response([
                'status'=> 'error',
                'message'=> 'Item not found'
            ],200);
        }

    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;
use App\Models\WishList;

class WishListController extends Controller
{
    //
    public function add(Request $request)
    {
        $attrs = $request->validate([
            "product_id" => "required|int",
        ]);

        $product = DB::select("SELECT * FROM wish_lists WHERE product_id=:pid AND user_id=:uid", [':pid' => $attrs['product_id'], ':uid' => auth()->user()->id]);
        // print_r($product); exit;
        if(!$product) {
            $wishList = WishList::create([
                'product_id'=> $attrs['product_id'],
                'user_id'=> auth()->user()->id,
            ]);
            if(!empty($wishList)) {
                return response([
                    'status' => 1,
                    'item' => $wishList
                ]);
            }else {
                return response([
                    'status' => 0,
                    'message' => 'Something went wrong.'
                ]);
            }
        } else {
            return response([
                'status' => 0,
                'message' => 'Already add in wish list.'
            ]);
        }
    }

    public function get()
    {
        $list = DB::select('SELECT * FROM wish_lists WHERE user_id=:uid', [':uid'=> auth()->user()->id]);
        if(is_array($list) && count($list) > 0)
        {
            $wishlist = [];
            foreach($list as $wish)
            {
                $product = Product::find($wish->product_id);
                $product->category = Category::find($product->category_id);
                $product->image_url = asset('images/');
                $wishlist[] = $product;
            }
            return response([
                'status'=> 1,
                'list'=> json_decode(json_encode($wishlist), true)
            ]);
        } else {
            return response([
                'status'=> 0,
                'message'=> 'List not found'
            ]);
        }
    }

    public function removeItem($id)
    {
        $item = DB::table('wish_lists')->where('product_id', $id)->where('user_id', auth()->user()->id)->first();
        if($item)
        {
            $item = WishList::find($item->id);
            if($item->delete())
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

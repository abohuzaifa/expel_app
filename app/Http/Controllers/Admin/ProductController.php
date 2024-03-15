<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;


class ProductController extends Controller
{
    //
    public function createProduct(Request $request)
    {
        
        // print_r($_POST); exit;
        $attrs = $request->validate([
            "p_name"=> "required|string",
            "p_name_ar"=> "required|string",
            "category_id"=> "required|int",
            "price"=> "required|numeric",
            ]);

        // check if tax in percentage
        if(strpos($request->tax, "%") !== false)
        {
            // echo $request->tax; exit;
            $tax_value = doubleval(str_replace("%","",$request->tax));
            $tax_value = doubleval(($request->price*$tax_value)/100);
            // echo $tax_value; exit;
        } else {
            $tax_value = $request->tax;
        }

        // check if discount in percentage
        if(strpos($request->discount, "%") !== false)
        {
            // echo $request->discount; exit;
            $discount_value = doubleval(str_replace("%","",$request->discount));
            $discount_value = doubleval(($request->price*$discount_value)/100);
            // echo $discount_value; exit;
        } else {
            $discount_value = $request->discount;
        }


        // check if product name already exists
        $product = DB::select("SELECT * FROM products WHERE p_name=:name", [':name' => $attrs['p_name']]);
        if(!empty($product))
        {
            return response([
                'status' => "0",
                'message' => "Product already exist"
            ], 200);
        }

        // check shop
        $category = DB::select("SELECT * FROM categories WHERE id=:id AND status=:status",[':id' => $attrs['category_id'], ':status' => 1]);
        if($category)
        {

            $images = [];
            if(isset($_FILES['images']))
            {
                // print_r($_FILES['images']); exit;
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('images'), $imageName);
                        // You may also store the image information in the database if needed.
                        $images[] = $imageName;
                    }
        
                }
            }
            $shop = Shop::where('created_by', auth()->user()->id)->first();
            $product = Product::create([
                "p_name"=> $attrs["p_name"],
                "p_name_ar"=> $attrs["p_name_ar"],
                "price"=> round($attrs["price"],2),
                "category_id"=> $attrs['category_id'],
                "shop_id" => $shop->id,
                "created_by"=> auth()->user()->id,
                "images"=> json_encode($images),
                "description" => $request->description,
                "tax" => round($tax_value, 2),
                "discount" => round($discount_value, 2),
                "taxable" => $request->taxable,
                "tax_inclusive" => $request->tax_inclusive,
            ]);

            if($product)
            {
                $product['image_base_url'] = asset("images/");
                return response([
                    "status" => "1",
                    "product" => $product
                ], 200);
            } else {
                return response([
                    "status"=> "0",
                    "message" => "Something went wrong"
                ]);
            }
        } else {
            return response([
                'status' => "0",
                "message" => "Shop not found"
            ], 200);
        }
    }

    public function updateProduct($id, Request $request)
    {
        
        // print_r(auth()->user()->id); exit;
        $attrs = $request->validate([
            "p_name"=> "required|string",
            "p_name_ar"=> "required|string",
            "category_id"=> "required|int",
            "price"=> "required|numeric",
            ]);

        // check if product name already exists
        $product = DB::select("SELECT * FROM products WHERE p_name=:name AND id != :id", [':name' => $attrs['p_name'], ':id' => $id]);
        if(!empty($product))
        {
            return response([
                'status' => "0",
                'message' => "Product already exist"
            ], 200);
        }

        // check if tax in percentage
        if(strpos($request->tax, "%") !== false)
        {
            // echo $request->tax; exit;
            $tax_value = doubleval(str_replace("%","",$request->tax));
            $tax_value = doubleval(($request->price*$tax_value)/100);
            // echo $tax_value; exit;
        } else {
            $tax_value = $request->tax;
        }

        // check if discount in percentage
        if(strpos($request->discount, "%") !== false)
        {
            // echo $request->discount; exit;
            $discount_value = doubleval(str_replace("%","",$request->discount));
            $discount_value = doubleval(($request->price*$discount_value)/100);
            // echo $discount_value; exit;
        } else {
            $discount_value = $request->discount;
        }


        // check shop
        $category = DB::select("SELECT * FROM categories WHERE id=:id AND status=:status",[':id' => $attrs['category_id'], ':status' => 1]);
        if($category)
        {
            $data = DB::table('products')->where('id','=', $id)->first();
            if($data)
            {
                $images = [];
                if(isset($_FILES['images']))
                {
                    // print_r($_FILES['images']); exit;
                    if ($request->hasFile('images')) {
                        foreach ($request->file('images') as $image) {
                            
                            $imageName = time() . '_' . $image->getClientOriginalName();
                            $image->move(public_path('images'), $imageName);
                            // You may also store the image information in the database if needed.
                            $images[] = $imageName;
                        }
            
                    }
                }

                $product = DB::table('products')->where('id', '=', $id)->update([
                    "p_name"=> $attrs["p_name"],
                    "p_name_ar"=> $attrs["p_name_ar"],
                    "price"=> $attrs["price"],
                    "category_id"=> $attrs["category_id"],
                    "images"=> count($images) > 0 ? json_encode($images) : $data->images,
                    "description" => isset($request->description) ? $request->description : $data->description,
                    "tax" => round($tax_value,2),
                    "discount" => round($discount_value,2),
                    "taxable" => isset($request->taxable) ? $request->taxable : $data->taxable,
                    "tax_inclusive" => isset($request->tax_inclusive) ? $request->tax_inclusive : $data->tax_inclusive,
                    "status" => isset($request->status) ? $request->status : $data->status
                ]);
    
                if($product)
                {
                    return response([
                        "status" => "1",
                        "product" => json_decode(json_encode(DB::table('products')->where('id','=', $id)->first()), true),
                        'image_base_url' => asset("images/"),
                    ], 200);
                } else {
                    return response([
                        "status"=> "0",
                        "message" => "Something went wrong"
                    ]);
                }

            } else {
                return response([
                    'status' => "0",
                    "message" => "Product not found"
                ], 200);
            }
        } else {
            return response([
                'status' => "0",
                "message" => "Shop not found"
            ], 200);
        }
    }

    public function getProduct($id)
    {
        $product = DB::select("SELECT * FROM products WHERE id=:id", [':id' => $id]);
        if($product)
        {
            $is_like = DB::table('wish_lists')->where('product_id', $product[0]->id)->get();
            $product[0]->is_like = 0;
            if(isset($is_like[0]->id))
            {
                $product[0]->is_like = 1;
            }
            return response([
                'status'=> '1',
                'product'=> json_decode(json_encode($product[0]), true),
                'image_base_url' => asset('images/')
            ],200);
        } else {
            return response([
                'status'=> '0',
                'message'=> 'Product not found.',
            ], 200);
        }
    }

    // Products List by shop ID
    public function productList($id){
        $shop = DB::select('SELECT * FROM shops WHERE id=:id AND status= 1', [':id'=> $id]);
        if($shop)
        {
            if(isset($shop[0]->category_id) && $shop[0]->category_id > 0)
            {
                $product = DB::select("SELECT * FROM products WHERE status=1 AND created_by =".$shop[0]->created_by);
                if(count($product) > 0){
                    foreach($product as $key => $item){
                        $is_like = DB::table('wish_lists')->where('product_id', $item->id)->get();
                        $product[$key]->is_like = 0;
                        if(isset($is_like[0]->id))
                        {
                            $product[$key]->is_like = 1;
                        }
                    }
                    return response([
                        "status"=> "1",
                        "products"=> json_decode(json_encode($product), true),
                        "shop" => json_decode(json_encode($shop[0]), true),
                        // "category" => json_decode(json_encode($category[0]), true),
                        "image_base_url" => asset('images/')
                    ],200);
                } else {
                    return response([
                        "status"=> "0",
                        "products"=> []
                    ]);
                }
            }
        } else {
            return response([
                'status'=> '0',
                'message'=> 'Invalid shop ID.'
            ], 200);
        }
    }

    public function delete($id)
    {
        $product = Product::find( $id );
        if($product->delete())
        {
            return response([
                'status'=> '1',
                'message' => "Product Delete successfully"
            ], 200);
        } else {
            return response([
                "status"=> "0",
                "message"=> "Something went wrong"
            ],404);
        }
    }

    public function products($id)
    {
        $products = Product::orderBy("id","desc")->paginate(30);
        // print_r(json_decode(json_encode($products), true));
        if(count($products) > 0)
        {
            return response([
                "status"=> "1",
                "products"=> json_decode(json_encode($products), true),
            ],200);
        } else {
            return response([
                "status"=> "0",
                "message"=> "Products not found"
            ],200);
        }
    }

    public function searchProduct(Request $request)
    {
        $attrs = $request->validate([
            'string' => 'required'
        ]);

        $products = DB::select('SELECT * FROM products WHERE p_name LIKE :pname', ['pname' => '%' . $attrs['string'] ."%"]);
        // print_r($products);
        return response([
            'status' => 1,
            'products' => json_decode(json_encode($products), true)
        ]);
    }

    public function sellerProducts()
    {
        $user = auth()->user();
        $count = Product::where('created_by', $user->id)->count();
        $products = DB::table('products')->where('created_by', $user->id)->orderByDesc('id')->paginate(10);

        return response([
            'status' => 1,
            'totalProducts' => $count,
            'products' => json_decode(json_encode($products), true)
        ]);
    }

   
}

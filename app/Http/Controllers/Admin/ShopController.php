<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    //
    public function create(Request $request){
        $attrs = $request->validate([
            "name"=> "required|string",
            "name_ar"=> "required|string",
            "category_id"=> "required|int",
            "location"=> "required|string",
            "reg_no"=> "required|string",
            ]);

        // Check if shop name already exists
        $shop = DB::select("SELECT * FROM shops WHERE name=:name", [':name' => $attrs['name']]);
        if(!empty($shop))
        {
            return response([
                'status' => "0",
                'message' => "Shop name already exist"
            ], 200);
        }
        // Check if category exists
        $category = Category::find($attrs["category_id"]);
        if(!$category)
        {
            return response([
                "status"=> "0",
                "message" => "Category Not found."
            ]);
        }

        // Check If file exists
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }

        // Create New record
        $shop = Shop::create([
            "name"=> $attrs["name"],
            "name_ar"=> $attrs["name_ar"],
            "logo"=> $file_name,
            "category_id"=> $category->id,
            "location"=> $request->location,
            "reg_no"=> $attrs["reg_no"],
            "created_by"=> auth()->user()->id,
            "description"=> $request->description,
        ]);
        if(!empty($file_name))
        {
            $imageUrl = asset('images/'.$file_name);
            $shop['imageUrl'] = $imageUrl;
        } 
        if($shop)
        {
            return response([
                "status"=> "1",
                "shop" => $shop
            ]);
        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong."
            ]);
        }
    }

    public function updateShop($id, Request $request){
        $attrs = $request->validate([
            "name"=> "required|string",
            // "name_ar"=> "required|string",
            "category_id"=> "required|int",
            "location"=> "required|string",
            "reg_no"=> "required|string",
            ]);

        // Check if shop name already exists
        $shop = DB::select("SELECT * FROM shops WHERE name=:name AND id != :id", [':name' => $attrs['name'], ':id' => $id]);
        if(!empty($shop))
        {
            return response([
                'status' => "0",
                'message' => "Shop name already exist"
            ], 200);
        }
        // Check if category exists
        $category = Category::find($attrs["category_id"]);
        if(!$category)
        {
            return response([
                "status"=> "0",
                "message" => "Category Not found."
            ]);
        }

        // Check If file exists
        $shop = DB::table("shops")->where("id","=", $id)->first();
        if(!empty($shop))
        {
            $file_name = "";
            if(isset($_FILES['image']))
            {
                $file_name = $this->upload($request);
            }
    
            // Create New record
            $shop = DB::table('shops')->where('id', '=', $id)->update([
                "name"=> $attrs["name"],
                "name_ar"=> $attrs["name_ar"],
                "logo"=> $file_name != "" ? $file_name : $shop->logo,
                "category_id"=> $category->id,
                "location"=> $request->location,
                "reg_no"=> $attrs["reg_no"],
                "description"=> isset($request->description) ? $request->description : $shop->description,
            ]);
            
            if($shop)
            {
                return response([
                    "status"=> "1",
                    "shop" => json_decode(json_encode(DB::table('shops')->where('id','=', $id)->first()), true),
                    'image_base_url' => asset('images/')
                ]);
            } else {
                return response([
                    "status"=> "0",
                    "message" => "Something went wrong."
                ]);
            }

        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong."
            ]);
        }
    }
    public function get($id){
        $shop = DB::select("SELECT * FROM shops WHERE id=:id", [':id' => $id]);
        if($shop)
        {
            // print_r($shop); exit;
            $shop = json_decode(json_encode($shop[0]), true);
            $file_name = $shop['logo'];
            if(!empty($file_name))
            {
                $imageUrl = asset('images/'.$file_name);
                $shop['imageUrl'] = $imageUrl;
            } 
            return response([
                "status"=> "1",
                "shop" => $shop
            ]);
        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong."
            ]);
        }
    }

    public function getAllShops($cat_id){
        // echo auth()->user()->id; exit;
        $category = DB::select("SELECT * FROM categories WHERE id=:id AND status=1", [":id"=> $cat_id]);
        if(count($category) > 0)
        {
            $shops = DB::select("SELECT * FROM shops WHERE status=1 AND FIND_IN_SET(:cat_id, category_id) > 0", [":cat_id" => $cat_id]);
            if(count($shops) > 0){
                // $shop['image_base_url'] = asset('images/');
                return response([
                    "status"=> "1",
                    "shops"=> json_decode(json_encode($shops), true),
                    "catgeory"=> json_decode(json_encode($category[0]), true),
                    "image_base_url" => asset('images/')
                ]);
            } else {
                return response([
                    "status"=> "0",
                    "message"=> "Shops not found"
                ]);
            }

        } else {
            return response([
                "status"=> "0",
                "message"=> "Category not found"
            ], 200);
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);

        return $imageName;
    }
    public function delete($id)
    {
        $shop = Shop::find( $id );
        $products = DB::select('SELECT id FROM products WHERE shop_id=:s_id', [':s_id' => $id]);
        // print_r($shop); exit;
        if($shop){
            if(count($products) == 0 && $shop->delete())
            {
                return response([
                    'status'=> '1',
                    'message' => "shop Delete successfully"
                ], 200);
            }else if($shop) {
                return response([
                    "status"=> "0",
                    "message"=> "You cannot delete this shop, This is use in products."
                ],200);
            }
        } else {
            return response([
                "status"=> "0",
                "message"=> "Shop not found"
            ],200);
        }
    }

    public function shops()
    {
        $shops = DB::table('shops')->where("status",1)->get();
        // print_r(json_decode(json_encode($shops), true));
        if(count($shops) > 0)
        {
            return response([
                "status"=> "1",
                "shops"=> json_decode(json_encode($shops), true),
            ],200);
        } else {
            return response([
                "status"=> "0",
                "message"=> "shops not found"
            ],200);
        }
    }
}

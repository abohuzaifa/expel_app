<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    //
    public function create(Request $request){
        // print_r($request); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            "name_ar"=> "required|string",
            ]);
        $category = DB::select("SELECT * FROM categories WHERE name=:name", [':name' => $attrs['name']]);
        if(!empty($category))
        {
            return response([
                'status' => "0",
                'message' => "Category name already exist"
            ], 200);
        }
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }
        $category = Category::create([
            "name"=> $attrs["name"],
            "name_ar"=> $attrs["name_ar"],
            "image"=> $file_name,
            "created_by"=> auth()->user()->id,
            "description"=> $request->description,
        ]);
        if($category)
        {
            if(!empty($file_name))
            {
                $imageUrl = asset('images/'.$file_name);
                $category['imageUrl'] = $imageUrl;
            } 
            return response([
                "status"=> "1",
                "category" => $category
            ]);
        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong."
            ]);
        }
    }

    public function update($id, Request $request){
        // print_r($request); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            // "name_ar"=> "required|string",
            ]);
        $category = DB::select("SELECT * FROM categories WHERE name=:name AND id !=:id", [':name' => $attrs['name'], ':id' => $id]);
        if(!empty($category))
        {
            return response([
                'status' => "0",
                'message' => "Category name already exist"
            ], 200);
        }
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }
        $data = DB::table('categories')->where('id', $id)->first();
        if($data)
        {
            $category = DB::table('categories')->where('id', $id)
                            ->update(
                                [
                                    "name"=> $attrs["name"],
                                    "name_ar"=> $attrs["name_ar"],
                                    "image"=> $file_name != "" ? $file_name : $data->image,
                                    "description"=> isset($request->description) ? $request->description : $data->description,
                                ]);
            if($category)
            {
                return response([
                    "status"=> "1",
                    "category" => json_decode(json_encode(DB::table('categories')->where('id','=', $id)->first()), true),
                    'image_base_url' => asset('/images'),
                    'message' => 'Category updated successfully'
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
                "message" => "Category not found."
            ]);
        }
    }
    public function getCategory($id){
        $category = DB::select("SELECT * FROM categories WHERE id=:id",[':id' => $id]);
        if($category)
        {
            $category = json_decode(json_encode($category[0]), true);
            $file_name = $category['image'];
            if(!empty($file_name))
            {
                $imageUrl = asset('images/'.$file_name);
                $category['imageUrl'] = $imageUrl;
            } 
            return response([
                "status"=> "1",
                "category" => $category
            ]);
        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong."
            ]);
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

    public function getAllCategories()
    {
        $categories = DB::select("SELECT * FROM categories WHERE status=1");
        if(count($categories) > 0) 
        {
            // print_r(json_decode(json_encode($categories), true)); exit;
            return response([
                'status'=> '1',
                'categories'=> json_decode(json_encode($categories), true),
                'image_base_url' => asset('images/')
            ]);
        } else {
            return response([
                'status'=> '0',
                'categories'=> []
            ], 404);
        }
    }
    public function delete($id)
    {
        $category = Category::find( $id );
        $shops = DB::select('SELECT id FROM shops WHERE category_id=:cat_id', [':cat_id' => $id]);
        // print_r($category); exit;
        if($category){
            if(count($shops) == 0 && $category->delete())
            {
                return response([
                    'status'=> '1',
                    'message' => "Category Delete successfully"
                ], 200);
            }else if($category) {
                return response([
                    "status"=> "0",
                    "message"=> "You cannot delete this category, This is use in shops."
                ],200);
            }
        } else {
            return response([
                "status"=> "0",
                "message"=> "Category not found"
            ],200);
        }
    }

    public function categories()
    {
        $categories = DB::table('categories')->where("status",1)->get();
        // print_r(json_decode(json_encode($categories), true));
        if(count($categories) > 0)
        {
            return response([
                "status"=> "1",
                "categories"=> json_decode(json_encode($categories), true),
            ],200);
        } else {
            return response([
                "status"=> "0",
                "message"=> "categories not found"
            ],200);
        }
    }

    public function adminChoiceCategories()
    {
        $categories = DB::table("categories")->where("status","=", 1)->where("admin_choice","=", 1)->get();
        return response([
            "status" => 1,
            "categories" => json_decode(json_encode($categories), true)
        ]);
    }

    public function sellerCategories()
    {
        $shop = Shop::where('created_by', auth()->user()->id)->first();
        // print_r($shop); exit;
        $categoryIdsArray = explode(',', $shop->category_id);
        // print_r($categoryIdsArray); exit;
        $categories = Category::whereIn('id', $categoryIdsArray)->get();

        return response([
            "status" => 1,
            "categories" => json_decode(json_encode($categories), true)
        ]);
    }
}

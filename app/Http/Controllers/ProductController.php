<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['perPage'] = 10;
        $data['Items'] = Product::join('shops', 'products.shop_id', '=', 'shops.id')
                                ->select('products.*', 'shops.name as shop_name')
                                ->paginate($data['perPage']);
                                // echo "<pre>"; print_r($data['Items']); exit;
        return view('product.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['category'] = Category::pluck('name','id')->all();
        $data['shop'] = Shop::pluck('name','id')->all();
        return view('product.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // print_r($_POST); exit;
        $data = $_POST;
        // check if tax in percentage
        if(strpos($data['tax'], "%") !== false)
        {
            // echo $request->tax; exit;
            $tax_value = doubleval(str_replace("%","",$data['tax']));
            $tax_value = doubleval(($data['price']*$tax_value)/100);
            // echo $tax_value; exit;
        } else {
            $tax_value = doubleval($data['tax']);
        }

        // check if discount in percentage
        if(strpos($data['discount'], "%") !== false)
        {
            // echo $data['discount']; exit;
            $discount_value = doubleval(str_replace("%","",$data['discount']));
            $discount_value = doubleval(($data['price']*$discount_value)/100);
            // echo $discount_value; exit;
        } else {
            $discount_value = doubleval($data['discount']);
        }


        // check if product name already exists
        $product = DB::select("SELECT * FROM products WHERE p_name=:name", [':name' => $data['name']]);
        if(!empty($product))
        {
            return redirect()->route('product.create')->with('error', trans('lang.product_exist_msg'));
        }

        // check shop
        $category = DB::select("SELECT * FROM categories WHERE id=:id AND status=:status",[':id' => $data['category_id'], ':status' => 1]);
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
    
            $product = Product::create([
                'category_id' => $data['category_id'],
                'shop_id' => $request->input('shop_id'),
                'p_name' => $request->input('name'),
                'images' =>  implode(',',$images),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'tax' => $tax_value,
                'discount' => $discount_value,
                'taxable' => isset($data['taxable']) ? 1 : 0,
                'tax_inclusive' => isset($data['tax_inclusive']) ? 1 : 0,
                'created_by' => Auth::user()->id,
                'status' =>1,
            ]);
    
            // $product->save();
            return redirect()->route('product.index')->with('success', trans('lang.product_create_message'));

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
        $category = Category::pluck('name','id')->all();
        $data['shop'] = Shop::pluck('name','id')->all();
        $data['category'] = $category;
        $data['product'] = $product;
        return view('product.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
         // print_r($_POST); exit;
         $data = $_POST;
         // check if tax in percentage
         if(strpos($data['tax'], "%") !== false)
         {
             // echo $request->tax; exit;
             $tax_value = doubleval(str_replace("%","",$data['tax']));
             $tax_value = doubleval(($data['price']*$tax_value)/100);
             // echo $tax_value; exit;
         } else {
             $tax_value = doubleval($data['tax']);
         }
 
         // check if discount in percentage
         if(strpos($data['discount'], "%") !== false)
         {
             // echo $data['discount']; exit;
             $discount_value = doubleval(str_replace("%","",$data['discount']));
             $discount_value = doubleval(($data['price']*$discount_value)/100);
             // echo $discount_value; exit;
         } else {
             $discount_value = doubleval($data['discount']);
         }
 
 
         // check if product name already exists
         $p_data = DB::select("SELECT * FROM products WHERE p_name=:name AND id != :id", [':name' => $data['name'], ':id' => $product->id]);
         if(!empty($p_data))
         {
             return redirect()->route('product.edit', $product->id)->with('error', trans('lang.product_exist_msg'));
         }
 
         // check shop
         $category = DB::select("SELECT * FROM categories WHERE id=:id AND status=:status",[':id' => $data['category_id'], ':status' => 1]);
         if($category)
         {
             $images = [];
             if(isset($_FILES['images']))
             {
                $prev_images = json_decode($product->images, true);
                if(is_array($prev_images))
                {
                    removeImages($prev_images, 1); 
                }
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
     
             $product = DB::table('products')->where('id', "=", $product->id)->update([
                 'category_id' => $request->input('category_id'),
                 'shop_id' => $request->input('shop_id'),
                 'p_name' => $request->input('name'),
                 'images' =>  count($images) > 0 ? json_encode($images) : $product->images,
                 'description' => $request->input('description'),
                 'price' => $request->input('price'),
                 'tax' => $tax_value,
                 'discount' => $discount_value,
                 'taxable' => isset($data['taxable']) ? 1 : 0,
                 'tax_inclusive' => isset($data['tax_inclusive']) ? 1 : 0,
                 'status' => isset($data['status']) ? 1 : 0,
             ]);
     
             // $product->save();
             return redirect()->route('product.index')->with('success', trans('lang.product_update_message'));
 
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('product.index')->with('success', trans('lang.product_delete_message'));
    }


}

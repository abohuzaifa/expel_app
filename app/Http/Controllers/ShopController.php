<?php

namespace App\Http\Controllers;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop =Shop::all();
         return view('shop.index',compact('shop'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category =Category::all();
        return view('shop.create',compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $_POST;

        if(!isset($data['categories']))
        {
            return redirect()->route('shop.create')->with('error', trans("lang.select_atleast_one_category"));
        }
        // Check if shop name already exists
        $shop = DB::select("SELECT * FROM shops WHERE name=:name", [':name' => $data['name']]);
        if(!empty($shop))
        {
            return redirect()->route('shop.create')->with('error', trans("lang.already_exist"));
        }
        $imageName = "";
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imageName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('images'), $imageName);
        }

         // Create New record
         $shop = Shop::create([
            "name"=> $data["name"],
            "logo"=> $imageName,
            "category_id"=> implode(',', $data['categories']),
            "location"=> $data['address'],
            "reg_no"=> $data["reg_no"],
            "created_by"=> auth()->user()->id,
            "description"=> $data['description'],
            "latitude" => $data['latitude'],
            "longitude" => $data['longitude']
        ]);
        if($shop)
        {
            return redirect()->route('shop.index')->with('success', trans("lang.create_message"));
        } else {
            return redirect()->route('shop.create')->with('error', trans("lang.something_went_wrong"));
        }

    }

    public function edit(Shop $shop)
    {
            $category =Category::all();
            $data['shop'] = $shop;
            $data['category'] = $category;
            return view('shop.edit',$data);
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $_POST;
        // print_r($shop); exit;
        if(!isset($data['categories']))
        {
            return redirect()->route('shop.edit', $shop->id)->with('error', trans("lang.select_atleast_one_category"));
        }
        // Check if shop name already exists
        $shop1 = DB::select("SELECT * FROM shops WHERE name=:name AND id != :id", [':name' => $data['name'], ':id' => $shop->id]);
        if(!empty($shop1))
        {
            return redirect()->route('shop.edit', $shop->id)->with('error', trans("lang.already_exist"));
        }
        $imageName = "";
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imageName = time() . '.' . $request->image->extension();

            $request->image->move(public_path('images'), $imageName);
        }

        $shop2 = DB::table('shops')->where('id', '=', $shop->id)->update([
            "name"=> $data["name"],
            "logo"=> $imageName != "" ? $imageName : $shop->logo,
            "category_id"=> implode(',', $data['categories']),
            "location"=> $data['address'],
            "reg_no"=> $data["reg_no"],
            "description"=> $data['description'],
            "latitude" => $data['latitude'],
            "longitude" => $data['longitude']
        ]);
        
        if($shop2)
        {
            return redirect()->route('shop.index')->with('success', trans("lang.update_message"));
        } else {
            return redirect()->route('shop.edit', $shop->id)->with('error', trans("lang.make_some_changes_first"));
        }
    }
}

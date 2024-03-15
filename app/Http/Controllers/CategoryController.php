<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category =Category::all();
         return view('category.index',compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $imageName = time() . '.' . $request->file->extension();

            $request->file->move(public_path('uploads'), $imageName);
        }
        $category = new Category;
        $category->name = $request->name;
        $category->image = $imageName;
        $category->description = $request->description;
        $category->save();

        return redirect()->route('category.index')->with('success', trans("lang.create_message"));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        if(isset($_GET["choice"]))
        {
            $id = $_GET['id'];
            $update=DB::table("categories")->where("id","=",$id)->update([
                "admin_choice" => $_GET["choice"] == 1 ? 0 : 1
            ]);
            return redirect()->route('category.index')->with('success', ($_GET["choice"] == 1 ? trans("lang.add_favourite_message") : trans("lang.remove_favourite_message")));
        } else {
            return view('category.edit',compact('category'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string',
            // 'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $cat = DB::select("SELECT * FROM categories WHERE name=:name AND id !=:id", [':name' => $_POST['name'], ':id' => $category->id]);
        if(!empty($cat))
        {
            return redirect()->route('category.index', $category->id)->with('success', trans("lang.update_message"));
        }
        $imageName = "";
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $imageName = time() . '.' . $request->file->extension();

            $request->file->move(public_path('uploads'), $imageName);
            $category->image = $imageName;
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        return redirect()->route('category.index', $category->id)->with('success', trans("lang.update_message"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('category.index')->with('success', trans("lang.delete_message"));
    }


}

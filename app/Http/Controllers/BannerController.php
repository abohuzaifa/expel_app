<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    //
    public function index()
    {
        $data['perPage'] = 10;
        $data['banner'] =Banner::orderBy('id','DESC')->paginate($data['perPage']);
        return view('banners.index',$data);
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        $imageName = "";
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    // echo "success"; exit;
                    $user = auth()->user();
                    removeImages($user->image);
                    $imageName = time() . '.' . $request->image->extension();
    
                    $request->image->move(public_path('images'), $imageName);
                } else {
                    return redirect()->route('banners.create')
                        ->with('error',trans('lang.add_atleast_one_image'));
                }

        Banner::create([
            'slug' => $_POST['slug'],
            'image' => $imageName,
            'status' => 1
        ]);

        return redirect()->route('banners.index')
                        ->with('success',trans('lang.create_message'));
    }

    public function edit(Banner $banner)
    {
            return view('banners.edit',compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $imageName = $banner->image;
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    // echo "success"; exit;
                    removeImages($imageName);
                    $user = auth()->user();
                    removeImages($user->image);
                    $imageName = time() . '.' . $request->image->extension();
    
                    $request->image->move(public_path('images'), $imageName);
                }
        DB::table('banners')->where('id', $banner->id)->update([
            'slug' => $_POST['slug'],
            'image' => $imageName,
            'status' => 1
        ]);

        return redirect()->route('banners.index')
                        ->with('success',trans('lang.update_message'));
    }

    public function destroy($id)
    {
        Banner::find($id)->delete();
        return redirect()->route('banners.index')
                        ->with('success',trans('lang.delete_message'));
    }

    public function sellers_list()
    {
        $data['perPage'] = 10;
        $data['sellers'] = DB::table('users')->where('user_type',1)->orderByDesc('id')->paginate($data['perPage']);
        return view('users.sellers_list', $data);
    }

    public function banner_active($id)
    {
        $user = Banner::find($id)->update(['status'=>1]);
        return redirect()->back()->with('success', trans('lang.status_active_success'));
    }
    public function banner_inactive($id)
    {
        $user = Banner::find($id)->update(['status'=>0]);
        return redirect()->back()->with('success', trans('lang.status_deactive_success'));
    }

    public function getBanners()
    {
        $banners = Banner::where('status', 1)->get();
        // print_r($banners);
        foreach($banners as $key => $banner)
        {
            $banners[$key]->image_base_url = asset('images/');
        }
        return response([
            'status' => 1,
            'banners' => json_decode(json_encode($banners), true)
        ]);
    }
}

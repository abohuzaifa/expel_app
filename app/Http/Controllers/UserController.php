<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Wallet;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['perPage'] = 10;
        $data['buyers'] = DB::table('users')->orderByDesc('id')->paginate($data['perPage']);
        return view('users.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::pluck('name','id')->all();
        return view('users.create',compact('category'));
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
        if(strlen($data['password']) < 4)
        {
            return redirect()->route('users.create')
                        ->with('error', trans('lang.password_lenth_short'));
        }
        if($data['password'] != $data['confirm-password'])
        {
            return redirect()->route('users.create')
                        ->with('error', trans('lang.password_does_not_match'));
        }
        $user = DB::select("SELECT * FROM users WHERE mobile=:mobile", [':mobile' => $_POST['mobile']]);
        if(count($user) > 0)
        {
            return redirect()->route('users.create')
                        ->with('error', trans('lang.mobile_already_exist'));
        }

        $user = DB::select("SELECT * FROM users WHERE email=:email", [':email' => $_POST['email']]);
        if(count($user) > 0)
        {
            return redirect()->route('users.create')
                        ->with('error', trans('lang.email_already_exist'));
        }

        if(isset($_POST['is_seller']) && $_POST['is_seller'] == 1)
        {
            $imageName = "";
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = time() . '.' . $request->image->extension();

                $request->image->move(public_path('images'), $imageName);
            }
            $user = User::create([
                'name' => $_POST['name'],
                'mobile' => $_POST['mobile'],
                'email' => $_POST['email'],
                'password' => bcrypt($_POST['password']),
                'image' => $imageName,
                'street_address' => $_POST['street_address'],
                'city' => $_POST['city'],
                'postal_code' => $_POST['postal_code'],
                'state' => $_POST['state'],
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude'],
                'user_type' => 1
            ]);

            if($user)
            {
                // Create New record
                $shop = Shop::create([
                    "name"=> $_POST["shop_name"],
                    "category_id"=> $_POST['category'],
                    "location"=> $_POST['street_address'],
                    "reg_no"=> $_POST['reg_no'],
                    "created_by"=> auth()->user()->id,
                    'latitude' => $_POST['latitude'],
                    'longitude' => $_POST['longitude'],
                ]);

                Wallet::create([
                    'user_id' => $user->id
                ]);
                
                return redirect()->route('users.index')
                ->with('success', trans('lang.create_message'));
            } else {
                return redirect()->route('users.create')
                ->with('error', trans('lang.something_went_wrong'));
            }
        } else if(isset($_POST['is_buyer']) && $_POST['is_buyer'] == 1)
        {
            $imageName = "";
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = time() . '.' . $request->image->extension();

                $request->image->move(public_path('images'), $imageName);
            }
            $user = User::create([
                'name' => $_POST['name'],
                'mobile' => $_POST['mobile'],
                'email' => $_POST['email'],
                'image' => $imageName,
                'password' => bcrypt($_POST['password']),
                'street_address' => $_POST['street_address'],
                'city' => $_POST['city'],
                'postal_code' => $_POST['postal_code'],
                'state' => $_POST['state'],
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude'],
                'user_type' => 2
            ]);

            if($user)
            {
                Wallet::create([
                    'user_id' => $user->id
                ]);
                return redirect()->route('users.index')
                ->with('success', trans('lang.create_message'));
            } else {
                return redirect()->route('users.create')
                ->with('error', trans('lang.something_went_wrong'));
            }
        } else if(isset($_POST['is_admin']) && $_POST['is_admin'] == 1)
        {
            $imageName = "";
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imageName = time() . '.' . $request->image->extension();

                $request->image->move(public_path('images'), $imageName);
            }
            $user = User::create([
                'name' => $_POST['name'],
                'mobile' => $_POST['mobile'],
                'email' => $_POST['email'],
                'password' => bcrypt($_POST['password']),
                'status' => 1,
                'image' => $imageName
            ]);
            
            if($user)
            {
                return redirect()->route('users.index')
                ->with('success', trans('lang.create_message'));
            } else {
                return redirect()->route('users.create')
                ->with('error', trans('lang.something_went_wrong'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit_user = User::find($id);
        // $roles = Role::pluck('name','name')->all();
        // $userRole = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('edit_user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // print_r($_FILES); exit;
        if(isset($_POST['is_password']) && $_POST['is_password'] == 1)
        {
            DB::table('users')->where('id', $id)->update([
                'password' => bcrypt($_POST['password'])
            ]);
        } else {
            $name = strip_tags($_POST['name']);
            $email = strip_tags($_POST['email']);
            $mobile = $_POST['mobile'];
            $city = strip_tags($_POST['city']);
            $state = strip_tags($_POST['state']);
            $country = strip_tags($_POST['country']);
            $street_address = strip_tags($_POST['street_address']);
            $latitude = strip_tags($_POST['latitude']);
            $longitude = strip_tags($_POST['longitude']);  
            $twitter = strip_tags($_POST['twitter']); 
            $facebook = strip_tags($_POST['facebook']); 
            $instagram = strip_tags($_POST['instagram']); 
            $linkedin = strip_tags($_POST['linkedin']);         
            // echo $name; exit;
    
            $user = DB::select("SELECT * FROM users WHERE mobile=:mobile AND id != $id", [':mobile' => $_POST['mobile']]);
            if(count($user) > 0)
            {
                if(isset($_POST['action']) && $_POST['action'] == "user_update")
                {
                    return redirect()->route('users.edit', $id)
                            ->with('error', trans('lang.mobile_already_exist'));
                } else {
                    return redirect()->route('users.profile')
                            ->with('error', trans('lang.mobile_already_exist'));
                }
                
            }
    
            $user = DB::select("SELECT * FROM users WHERE email=:email AND id != $id", [':email' => $_POST['email']]);
            if(count($user) > 0)
            {
                if(isset($_POST['action']) && $_POST['action'] == "user_update")
                {
                    return redirect()->route('users.edit', $id)
                            ->with('error', trans('lang.email_already_exist'));
                } else {
                    return redirect()->route('users.profile')
                            ->with('error', trans('lang.email_already_exist'));
                }
            }
            $imageName = "";
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    // echo "success"; exit;
                    $user = auth()->user();
                    removeImages($user->image);
                    $imageName = time() . '.' . $request->image->extension();
    
                    $request->image->move(public_path('images'), $imageName);
                }
            $user = DB::table('users')->where('id', $id)->update([
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'image' => $imageName,
                'street_address' => $street_address,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'twitter' => $twitter,
                'facebook' => $facebook,
                'instagram' => $instagram,
                'linkedin' => $linkedin
            ]); 

        }
        if(isset($_POST['action']) && $_POST['action'] == "user_update")
        {
            return redirect()->route('users.index')
            ->with('success',trans('lang.update_message'));
        } else {
            return redirect()->route('profile')
            ->with('success',trans('lang.update_message'));
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success',trans('lang.delete_message'));
    }

    public function sellers_list()
    {
        $data['perPage'] = 10;
        $data['sellers'] = DB::table('users')->where('user_type',1)->orderByDesc('id')->paginate($data['perPage']);
        return view('users.sellers_list', $data);
    }

    public function sellers_active($id)
    {
        $user = User::find($id)->update(['status'=>1]);
        return redirect()->back()->with('success', trans('lang.status_active_success'));
    }
    public function sellers_inactive($id)
    {
        $user = User::find($id)->update(['status'=>0]);
        return redirect()->back()->with('success', trans('lang.status_deactive_success'));
    }
    public function change_password(Request $request, $id)
    {

    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\CardDetail;
use App\Models\Shop;
use App\Rules\ValidMobileNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    //Register User
    public function register(Request $request)
    {
        // print_r($request->name_ar); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            "email"=> "required|email|unique:users,email",
            "password"=> "required|min:6|confirmed",
            'mobile' => 'required|unique:users',
            'user_type'=> 'required|int',
        ]);
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }
        $randomNumber = rand(100000, 999999);
        $user = User::create([
            "name"=> $attrs["name"],
            "name_ar"=> $request->name_ar,
            "email"=> $attrs["email"],
            "mobile" => $attrs["mobile"],
            "user_type" => $attrs['user_type'],
            "password"=> bcrypt($attrs["password"]),
            "image"=> $file_name,
            "otp"=> $randomNumber,
            "street_address" => $request->address,
            "status"=> 1,
            "country" => $request->country,
        ]);
        if(!empty($file_name))
        {
            $imageUrl = asset('images/'.$file_name);
            $user['imageUrl'] = $imageUrl;
        } 
       
        if($user)
        {
            Wallet::create([
                'user_id' => $user->id
            ]);
            return response([
                'users' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
            ]);
        } else {
            return response([
                "message" => "Something went wrong."
            ]);
        }
        
    }

    //Register User
    public function sellerRegister(Request $request)
    {
        // print_r($request->name_ar); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            "email"=> "required|email|unique:users,email",
            "password"=> "required|min:6|confirmed",
            'mobile' => 'required|unique:users',
            'user_type'=> 'required|int',
            'shop_name' => 'required',
            'category_id' => 'required',
            'reg_no' => 'required'
        ]);
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }
        $randomNumber = rand(100000, 999999);
        $user = User::create([
            "name"=> $attrs["name"],
            "name_ar"=> $request->name_ar,
            "email"=> $attrs["email"],
            "mobile" => $attrs["mobile"],
            "user_type" => $attrs['user_type'],
            "password"=> bcrypt($attrs["password"]),
            "image"=> $file_name,
            "otp"=> $randomNumber,
            "street_address" => $request->address,
            "status"=> 0,
            "country" => $request->country,
        ]);
        if(!empty($file_name))
        {
            $imageUrl = asset('images/'.$file_name);
            $user['imageUrl'] = $imageUrl;
        } 
       
        if($user)
        {
            // Create New record
            $shop = Shop::create([
                "name"=> $_POST["shop_name"],
                "category_id"=> $_POST['category_id'],
                "reg_no"=> $_POST['reg_no'],
                "created_by"=> $user->id,
            ]);

            Wallet::create([
                'user_id' => $user->id
            ]);
            return response([
                'users' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
            ]);
        } else {
            return response([
                "message" => "Something went wrong."
            ]);
        }
        
    }

    public function setLocation(Request $req)
    {
        $req->validate([
            // 'city' => 'required|string',
            // 'street_address' => "required",
            // "state" => "required",
            // "postal_code" => "required",
            "latitude" => "required",
            "longitude" => "required"
        ]);

        
        $user = auth()->user();
        $data = [
            'city' => $req->city ?? $user->city,
            'street_address' => $req->street_address ?? $user->street_address,
            "state" => $req->state ?? $user->state,
            "postal_code" => $req->postal_code ?? $user->postal_code,
            "latitude" => $req->latitude ?? $user->latitude,
            "longitude" => $req->longitude ?? $user->longitude,
        ];      

        $update = DB::table("users")->where("id","=", $user->id)->update($data);
        // print_r($user); exit;
        if($user->user_type == 1)
        {
            // echo "success"; exit;
            DB::table('shops')->where('created_by', $user->id)->update([
                    "location"=> $req->street_address ?? $user->street_address,
                    'latitude' => $req->latitude ?? $user->latitude,
                    'longitude' => $req->longitude ?? $user->longitude,
            ]);
        }
        // if($update){
            return response([
                "status" => 1,
                "msg" => "success"
            ]);
        // } else {
            // return response([
            //     "status" => 0,
            //     "msg" => "Something went wrong"
            // ]);
        // }
    }
    public function updateUser(Request $request)
    {
        // print_r($request->name_ar); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            "email"=> "required|email",
            'mobile' => 'required',
        ]);
        
        $user = auth()->user();
        // print_r($user); exit;
        if($user->user_type == 0)
        {
            return response([
                "status" => 0,
                "message" => "This is not a valid user."
            ]);
        }
        $data = DB::select("SELECT * FROM users WHERE email=:email AND id != :id",[':email' => $attrs['email'], ':id' => $user->id]);
        // print_r($data); exit;
        if(count($data) > 0)
        {
            return response([
                "status" => 0,
                "message" => "Email already taken."
            ]);
        }
        $data = DB::select("SELECT * FROM users WHERE mobile=:mobile AND id != :id",[':mobile' => $attrs['mobile'], ':id' => $user->id]);
        // print_r($data); exit;
        if(count($data) > 0)
        {
            return response([
                "status" => 0,
                "message" => "Mobile number already taken."
            ]);
        }
        $file_name = "";
        if(isset($_FILES['image']))
        {
            removeImages($user->image); 
            $file_name = $this->upload($request);
        }

          
        $user = DB::table("users")->where("id","=", $user->id)->update([
            "name"=> $attrs["name"],
            "email"=> $attrs["email"],
            "mobile" => $attrs["mobile"],
            "image" => $file_name != "" ? $file_name : $user->image,
            'city' => $request->city ?? $user->city,
            'street_address' => $request->street_address ?? $user->street_address,
            "state" => $request->state ?? $user->state,
            "postal_code" => $request->postal_code ?? $user->postal_code,
            "latitude" => $request->latitude ?? $user->latitude,
            "longitude" => $request->longitude ?? $user->longitude,
        ]);
        
       

            return response([
                'status' => 1,
                'message' => "User Updated Successfully",
            ]);
        
    }
    // login user
    public function login(Request $request)
    {
        $attrs = $request->validate([
            "mobile"=> "required|string",
            "password"=> "required|min:6",
        ]);

       $user = User::where('mobile', $attrs['mobile'])->first();
    //    print_r($user->mobile); exit;
    if($user->user_type == 1 && $user->status == 0)
    {
        return response([
            'message' => "Your account is inactive pls contact to the support to make active your account.",
        ], 403);
    } else if($user->user_type == 2 && $user->status == 0)
    {
        return response([
            'message' => "Your account status is inactive pls contact to the support to make active your account.",
        ], 403);
    }
       if(!Auth::attempt($attrs)) {
        return response([
            'message' => "Invalid Credentials.",
        ], 403);
       }

        // return redirect()->route("")->with("success","");
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ], 200);
    }

    //logout user
    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message'=> 'Logout success.',
            ],200);
    }

    // user detail
    public function user(){
        
        $user = auth()->user();
        if($user)
        {
            $file_name = auth()->user()->image;
            if(!empty($file_name))
            {
                $imageUrl = asset('images/'.$file_name);
                auth()->user()->imageUrl = $imageUrl;
            } 
            return response([
                'user'=> json_decode(json_encode($user), true),    
            ], 200);

        } else {
            return response([
                'message'=> 'SESSION expired',    
            ], 200);
        }
    }
    public function reset(Request $request){
        $attrs = $request->validate([
            'mobile'=> 'required',
            "password"=> "required|min:6|confirmed",
        ]);

        $users = DB::select('SELECT * FROM users WHERE mobile=:mobile AND id > 0', [':mobile' => $attrs['mobile']]);
        // print_r($users);
        if(count($users) > 0){
            $user = DB::update('UPDATE users SET password=:password WHERE id=:id', [':password' => bcrypt($attrs["password"]), ':id' => $users[0]->id]);
            if($user)
            {
                return response([
                    'message' => "Password Update Successfully.",
                ], 200);
            }
        }else {
            return response([
                'message' => "User not found.",
            ], 200);
        }
    }

    public function otpVarification(Request $request){
        $attrs = $request->validate([
            "otp"=> "required|max:6|string",
            // "user_id"=> 'required|int',
        ]);

        $user = DB::select('SELECT * FROM users WHERE otp=:otp', [':otp'=> $attrs['otp']]);
        $user = json_decode(json_encode($user), true)[0];
        print_r(decrypt($user['password'])); exit;
        if(count($user) > 0){
            $attrs = [
                'mobile' => $user['mobile'],
                'password'=> decrypt($user['password']),
            ];
            $userUpdate = DB::update('UPDATE users SET status=:status WHERE id=:id', [':id' => $user['id'],':status' => 1]);
            if($userUpdate)
            {
                if(!Auth::attempt($attrs)) {
                    return response([
                        'message' => "Invalid OTP code.",
                    ], 403);
                   }
            
                    // return redirect()->route("")->with("success","");
                    return response([
                        'user' => auth()->user(),
                        'token' => auth()->user()->createToken('secret')->plainTextToken,
                    ], 200);
            }else{
                if(!Auth::attempt($attrs)) {
                    return response([
                        'message' => "Invalid OTP code.",
                    ], 403);
                   }
            
                    // return redirect()->route("")->with("success","");
                    return response([
                        'user' => auth()->user(),
                        'token' => auth()->user()->createToken('secret')->plainTextToken,
                    ], 200);
            }
        }else{
            return response([
                'message'=> 'OTP invalid',
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
        $user = User::find( $id );
        $categories = DB::select('SELECT id FROM categories WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        $shops = DB::select('SELECT id FROM shops WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        $products = DB::select('SELECT id FROM products WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // print_r($categories); exit;
        if($user){
            if(count($categories) == 0 && count($shops) == 0 && count($products) == 0 && $user->delete())
            {
                return response([
                    'status'=> 'success',
                    'message' => "User Delete successfully"
                ], 200);
            }else if($user) {
                return response([
                    "status"=> "success",
                    "message"=> "You cannot delete this user, This is use in category, shops and products."
                ],200);
            }
        } else {
            return response([
                "status"=> "success",
                "message"=> "User not found"
            ],200);
        }
    }

    public function resetRequest(Request $request)
    {
        $attrs = $request->validate([
            "mobile"=> "required",
        ]);

        $user = DB::select("SELECT * FROM users WHERE mobile=:mobile", [":mobile"=> $attrs['mobile']]);
        if($user)
        {
            return response([
                'status'=> 'success',
                'otp' => $user[0]->otp
            ],200);
        }else {
            return response([
                'status'=> 'success',
                'message'=> 'user Not found'
            ],200);
        }
    }
     
    public function userList($type = 1)
    {
        $users = DB::table('users')->where('user_type', $type)->get();

        if(count($users) > 0)
        {
            return response([
                'status'=> '1',
                'users' => json_decode(json_encode($users), true),
            ],200);
        } else {
            return response([
                'status'=> '0',
                'message' => "Users not found"
            ],200);
        }
    }
    public function updateProfileImage($id, Request $req)
    {
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($req);
        }

        $update = DB::table("users")->where("id", $id)->update([
            'image' => $file_name
        ]);
        if($update){
            return response([
                "status" => 1,
                "image_url" => asset("/images/".$file_name)
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "Something went wrong"
            ]);
        }
    }

    public function cardDetail(Request $req)
    {
        $data = $req->validate([
            'card_number' => "required",
            "cvv" => "required|int",
            "month" => "required|int",
            "year" => "required|int",
        ]);

        $card_data = CardDetail::create([
            'card_number' => $data['card_number'],
            "cvv" => $data['cvv'],
            "month" => $data['month'],
            "year" => $data['year'],
            "user_id" => auth()->user()->id
        ]);

        if($card_data)
        {
            return response([
                'status' => 1,
                "card" => json_decode(json_encode($card_data), true)
            ]);
        } else {
            return response([
                'status' => 0,
                "message" => "Something went wrong."
            ]);
        }
    }

    public function cardDetailUpdate($id,Request $req)
    {
        $data = $req->validate([
            'card_number' => "required",
            "cvv" => "required|int",
            "month" => "required|int",
            "year" => "required|int",
        ]);

        $card_data = CardDetail::where('id',$id)->update([
            'card_number' => $data['card_number'],
            "cvv" => $data['cvv'],
            "month" => $data['month'],
            "year" => $data['year'],
        ]);

        if($card_data)
        {
            return response([
                'status' => 1,
                "message" => "Update Successfully."
            ]);
        } else {
            return response([
                'status' => 0,
                "message" => "Something went wrong."
            ]);
        }
    }

    public function deleteCardDetails($id)
    {
        $card = CardDetail::find( $id );
        
            if($card->delete())
            {
                return response([
                    'status'=> '1',
                    'message' => "Card detail delete successfully."
                ], 200);
            }else if($card) {
                return response([
                    "status"=> "0",
                    "message"=> "Some thing went wrong."
                ],200);
            }
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\CardDetail;
use App\Models\BankAccount;
use App\Models\Notification;
use App\Models\Shop;
use App\Models\UserNotificationSetting;
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
            $notification = new Notification();
            $notification->user_id = $user->id; // Assuming the user is authenticated
            $notification->message = 'Your account registered Successfully';
            $notification->page = 'profile';
            $notification->save();
            return response([
                'users' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
                'imageUrl' => $imageUrl ?? "",
                'notifictionSettings' => UserNotificationSetting::firstOrCreate(
                    ['user_id' => $user->id],
                    UserNotificationSetting::getDefaultSettings()
                )
            ]);
        } else {
            return response([
                "message" => "Something went wrong."
            ]);
        }
        
    }

    public function bankList()
    {
        $banks = Bank::where('status', 1)->get();
        return response()->json(['banks' => $banks]);
    }

    public function updateVehicle(Request $req)
    {
        $attrs = $req->validate([
            'number_plate' => 'required|string|max:100',
            'vehicle_type' => 'required|integer',
            'driving_license' => 'required|string|max:100',
            'driving_license_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'vehicle_registration_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $user = auth()->user();

        if (!$user || (int) $user->user_type !== 2) {
            return response()->json([
                'status' => 0,
                'message' => 'Only drivers can update vehicle settings.',
            ], 403);
        }

        $drivingLicenseImage = $this->uploadDocument($req, 'driving_license_image', $user->driving_license_image);
        $vehicleRegistrationImage = $this->uploadDocument($req, 'vehicle_registration_image', $user->vehicle_registration_image);

        $requiresReverification = $user->number_plate !== $attrs['number_plate']
            || (int) $user->category_id !== (int) $attrs['vehicle_type']
            || $user->driving_license !== $attrs['driving_license']
            || $req->hasFile('driving_license_image')
            || $req->hasFile('vehicle_registration_image');

        $payload = [
            'number_plate' => $attrs['number_plate'],
            'category_id' => $attrs['vehicle_type'],
            'driving_license' => $attrs['driving_license'],
            'driving_license_image' => $drivingLicenseImage,
            'vehicle_registration_image' => $vehicleRegistrationImage,
        ];

        if ($requiresReverification) {
            $payload['verification_status'] = 'pending';
            $payload['verification_notes'] = null;
            $payload['verified_by'] = null;
            $payload['verified_at'] = null;
        }

        User::where('id', $user->id)->update($payload);

        $updatedUser = User::find($user->id);

        return response()->json([
            'status' => 1,
            'message' => 'Vehicle settings updated successfully.',
            'vehicle' => $this->vehiclePayload($updatedUser),
        ]);
    }

    public function createDriver(Request $req)
    {
        $attrs = $req->validate([
            "name"=> "required|string",
            'email' => 'nullable|email|unique:users,email',
            "password"=> "required|min:6|confirmed",
            'mobile' => 'required|unique:users',
            'user_type'=> 'required|int',
            'vehicle_type' => 'required|int',
            'number_plate' => 'nullable|string|max:100',
            'driving_license' => 'nullable|string|max:100',
            'bank_id' => 'nullable|integer',
            'bank_account' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:100',
            'driving_license_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'vehicle_registration_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $randomNumber = rand(100000, 999999);
        $drivingLicenseImage = $this->uploadDocument($req, 'driving_license_image');
        $vehicleRegistrationImage = $this->uploadDocument($req, 'vehicle_registration_image');

        $user = User::create([
            "name"=> $attrs["name"],
            "email"=> $attrs['email'] ?? null,
            "mobile" => $attrs["mobile"],
            "user_type" => $attrs['user_type'],
            "password"=> bcrypt($attrs["password"]),
            "otp"=> $randomNumber,
            "street_address" => $req->address,
            "status"=> 1,
            "category_id" => $req->vehicle_type,
            'number_plate' => $req->number_plate,
            "driving_license" => $req->driving_license ?? "",
            'driving_license_image' => $drivingLicenseImage,
            'vehicle_registration_image' => $vehicleRegistrationImage,
            "bank_id" => $req->bank_id ?? 0,
            "bank_account" => $req->bank_account,
            "name_ar" => $req->name_ar,
            'iban' => $req->iban,
            'verification_status' => 'pending',
        ]);

        if($user)
        {
            Wallet::create([
                'user_id' => $user->id
            ]);

            User::storeAppNotification($user->id, 'Your account registered Successfully', 'profile', 'account_updates');

            return response([
                'users' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
                'vehicle' => $this->vehiclePayload($user),
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

        $update = User::where('id', $user->id)->update($data);
        // print_r($user); exit;
       
        if($update){
            return response([
                "status" => 1,
                "msg" => "success"
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "Something went wrong"
            ]);
        }
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
          "device_token" => "required",
          'user_type' => 'required|int'
      ]);
      $data = $attrs;
      unset($data['device_token']);
     $user = User::where('mobile', $attrs['mobile'])->first();
     if($user)
     {//    print_r($user->mobile); exit;
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
          if($user->user_type != $request->user_type)
          {
                return response([
                    'message' => "user_type does not matched",
                ]);
          }
          if(!Auth::attempt($data)) {
              return response([
                  'message' => "Invalid Credentials.",
              ], 403);
          }

          DB::table('users')->where('mobile', $attrs['mobile'])->update([
              'device_token' => $attrs['device_token']
          ]);
              // return redirect()->route("")->with("success","");
              return response([
                  'user' => User::find(auth()->user()->id),
                  'token' => auth()->user()->createToken('secret')->plainTextToken,
                  'notifictionSettings' => UserNotificationSetting::firstOrCreate(
                        ['user_id' => $user->id],
                        UserNotificationSetting::getDefaultSettings()
                    )
              ], 200);
     } else {
          return response([
              'message' => "User not found",
          ], 403);
     }
  
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
                $notification = new Notification();
                $notification->user_id = $users[0]->id; // Assuming the user is authenticated
                $notification->message = 'Your account registered Successfully';
                $notification->page = 'profile';
                $notification->save();
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);

        return $imageName;
    }
    public function delete()
    {
        $user = User::find(auth()->user()->id);
        // $categories = DB::select('SELECT id FROM categories WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // $shops = DB::select('SELECT id FROM shops WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // $products = DB::select('SELECT id FROM products WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // print_r($categories); exit;
        if($user){
            if($user->delete())
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

    public function vehicleSettings()
    {
        $user = auth()->user();

        if (!$user || (int) $user->user_type !== 2) {
            return response()->json([
                'status' => 0,
                'message' => 'Only drivers have vehicle settings.',
            ], 403);
        }

        return response()->json([
            'status' => 1,
            'vehicle' => $this->vehiclePayload($user),
        ]);
    }

    public function contactDetails()
    {
        $user = auth()->user();
        $card = CardDetail::where('user_id', $user->id)->latest()->first();

        $addressParts = array_filter([
            $user->street_address ?: $user->address,
            $user->city,
            $user->state,
            $user->country,
            $user->postal_code,
        ]);

        return response()->json([
            'status' => 1,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'address' => [
                    'street_address' => $user->street_address ?: $user->address,
                    'city' => $user->city,
                    'state' => $user->state,
                    'country' => $user->country,
                    'postal_code' => $user->postal_code,
                    'full_address' => implode(', ', $addressParts),
                ],
                'payment_method' => $card ? $this->serializeCard($card) : null,
                'profile_image_url' => !empty($user->image) ? asset('images/' . $user->image) : null,
            ],
        ]);
    }

    protected function uploadDocument(Request $request, string $field, ?string $currentFile = null): ?string
    {
        if (!$request->hasFile($field) || !$request->file($field)->isValid()) {
            return $currentFile;
        }

        if ($currentFile) {
            removeImages($currentFile);
        }

        $file = $request->file($field);
        $fileName = uniqid($field . '_', true) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images'), $fileName);

        return $fileName;
    }

    protected function vehiclePayload(User $user): array
    {
        return [
            'vehicle_type' => $user->category_id,
            'number_plate' => $user->number_plate,
            'driving_license' => $user->driving_license,
            'driving_license_image' => $user->driving_license_image,
            'driving_license_image_url' => $user->driving_license_image ? asset('images/' . $user->driving_license_image) : null,
            'vehicle_registration_image' => $user->vehicle_registration_image,
            'vehicle_registration_image_url' => $user->vehicle_registration_image ? asset('images/' . $user->vehicle_registration_image) : null,
            'bank_id' => $user->bank_id,
            'bank_account' => $user->bank_account,
            'iban' => $user->iban,
            'verification_status' => $user->verification_status ?? 'pending',
            'verification_notes' => $user->verification_notes,
            'verified_at' => $user->verified_at,
            'is_verified' => ($user->verification_status ?? 'pending') === 'verified',
        ];
    }

    protected function serializeCard(CardDetail $card): array
    {
        return [
            'id' => $card->id,
            'card_number' => str_repeat('*', max(strlen($card->card_number) - 4, 0)) . substr($card->card_number, -4),
            'last_four' => substr($card->card_number, -4),
            'month' => $card->month,
            'year' => $card->year,
            'expiry' => sprintf('%02d/%s', (int) $card->month, $card->year),
        ];
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

    public function cardDetails()
    {
        $cards = CardDetail::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(fn (CardDetail $card) => $this->serializeCard($card))
            ->values();

        return response([
            'status' => 1,
            'cards' => $cards,
        ]);
    }

    public function showCardDetail($id)
    {
        $card = CardDetail::where('user_id', auth()->id())->find($id);

        if (!$card) {
            return response([
                'status' => 0,
                'message' => 'Card detail not found.',
            ], 404);
        }

        return response([
            'status' => 1,
            'card' => $this->serializeCard($card),
        ]);
    }

    public function cardDetail(Request $req)
    {
        $data = $req->validate([
            'card_number' => 'required|digits_between:12,19',
            'cvv' => 'required|digits_between:3,4',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|digits:4',
        ]);

        $card_data = CardDetail::create([
            'card_number' => $data['card_number'],
            'cvv' => $data['cvv'],
            'month' => sprintf('%02d', (int) $data['month']),
            'year' => $data['year'],
            "user_id" => auth()->user()->id
        ]);

        if($card_data)
        {
            return response([
                'status' => 1,
                'card' => $this->serializeCard($card_data)
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
            'card_number' => 'required|digits_between:12,19',
            'cvv' => 'required|digits_between:3,4',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|digits:4',
        ]);

        $card = CardDetail::where('user_id', auth()->id())->find($id);

        if (!$card) {
            return response([
                'status' => 0,
                'message' => 'Card detail not found.',
            ], 404);
        }

        $card->update([
            'card_number' => $data['card_number'],
            'cvv' => $data['cvv'],
            'month' => sprintf('%02d', (int) $data['month']),
            'year' => $data['year'],
        ]);

        return response([
            'status' => 1,
            'message' => 'Update Successfully.',
            'card' => $this->serializeCard($card->fresh()),
        ]);
    }

    public function deleteCardDetails($id)
    {
        $card = CardDetail::where('user_id', auth()->id())->find($id);

        if (!$card) {
            return response([
                'status' => '0',
                'message' => 'Card detail not found.',
            ], 404);
        }

        if($card->delete())
        {
            return response([
                'status'=> '1',
                'message' => 'Card detail delete successfully.'
            ], 200);
        }

        return response([
            'status'=> '0',
            'message'=> 'Some thing went wrong.'
        ],200);
    }

    // Bank Account Management APIs
    public function bankAccounts()
    {
        $bankAccounts = auth()->user()->bankAccounts()
            ->with('bank')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'bank_id' => $account->bank_id,
                    'bank_name' => $account->bank->name ?? null,
                    'account_holder_name' => $account->account_holder_name,
                    'account_number' => $account->maskAccountNumber(),
                    'account_number_full' => $account->account_number,
                    'branch_code' => $account->branch_code,
                    'iban' => $account->iban,
                    'verification_status' => $account->verification_status,
                    'is_primary' => $account->is_primary,
                    'verified_at' => $account->verified_at,
                ];
            });

        return response([
            'status' => 1,
            'bank_accounts' => $bankAccounts,
        ]);
    }

    public function addBankAccount(Request $req)
    {
        $data = $req->validate([
            'bank_id' => 'required|exists:banks,id',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        // Check if user already has 5 bank accounts
        $accountCount = BankAccount::where('user_id', $user->id)->count();
        if ($accountCount >= 5) {
            return response([
                'status' => 0,
                'message' => 'Maximum 5 bank accounts allowed.',
            ], 422);
        }

        $bankAccount = BankAccount::create([
            'user_id' => $user->id,
            'bank_id' => $data['bank_id'],
            'account_holder_name' => $data['account_holder_name'],
            'account_number' => $data['account_number'],
            'branch_code' => $data['branch_code'],
            'iban' => $data['iban'],
            'verification_status' => 'pending',
        ]);

        // Notify admin about new bank account submission
        User::storeAppNotification(
            $user->id,
            "Your bank account has been submitted for verification",
            'bank_accounts',
            'account_updates'
        );

        return response([
            'status' => 1,
            'message' => 'Bank account added successfully.',
            'bank_account' => [
                'id' => $bankAccount->id,
                'bank_id' => $bankAccount->bank_id,
                'account_holder_name' => $bankAccount->account_holder_name,
                'account_number' => $bankAccount->maskAccountNumber(),
                'branch_code' => $bankAccount->branch_code,
                'iban' => $bankAccount->iban,
                'verification_status' => $bankAccount->verification_status,
            ],
        ], 201);
    }

    public function updateBankAccount($id, Request $req)
    {
        $bankAccount = BankAccount::where('user_id', auth()->id())->find($id);

        if (!$bankAccount) {
            return response([
                'status' => 0,
                'message' => 'Bank account not found.',
            ], 404);
        }

        // Only allow update if not yet verified
        if ($bankAccount->verification_status !== 'pending') {
            return response([
                'status' => 0,
                'message' => 'Only pending bank accounts can be updated.',
            ], 422);
        }

        $data = $req->validate([
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
        ]);

        $bankAccount->update([
            'account_holder_name' => $data['account_holder_name'],
            'account_number' => $data['account_number'],
            'branch_code' => $data['branch_code'],
            'iban' => $data['iban'],
        ]);

        return response([
            'status' => 1,
            'message' => 'Bank account updated successfully.',
            'bank_account' => [
                'id' => $bankAccount->id,
                'bank_id' => $bankAccount->bank_id,
                'account_holder_name' => $bankAccount->account_holder_name,
                'account_number' => $bankAccount->maskAccountNumber(),
                'branch_code' => $bankAccount->branch_code,
                'iban' => $bankAccount->iban,
                'verification_status' => $bankAccount->verification_status,
            ],
        ]);
    }

    public function deleteBankAccount($id)
    {
        $bankAccount = BankAccount::where('user_id', auth()->id())->find($id);

        if (!$bankAccount) {
            return response([
                'status' => 0,
                'message' => 'Bank account not found.',
            ], 404);
        }

        // Only allow delete if not verified
        if ($bankAccount->verification_status === 'verified') {
            return response([
                'status' => 0,
                'message' => 'Cannot delete a verified bank account.',
            ], 422);
        }

        if ($bankAccount->delete()) {
            return response([
                'status' => 1,
                'message' => 'Bank account deleted successfully.',
            ]);
        }

        return response([
            'status' => 0,
            'message' => 'Something went wrong.',
        ], 500);
    }

    public function showBankAccount($id)
    {
        $bankAccount = BankAccount::where('user_id', auth()->id())
            ->with('bank')
            ->find($id);

        if (!$bankAccount) {
            return response([
                'status' => 0,
                'message' => 'Bank account not found.',
            ], 404);
        }

        return response([
            'status' => 1,
            'bank_account' => [
                'id' => $bankAccount->id,
                'bank_id' => $bankAccount->bank_id,
                'bank_name' => $bankAccount->bank->name ?? null,
                'account_holder_name' => $bankAccount->account_holder_name,
                'account_number' => $bankAccount->maskAccountNumber(),
                'account_number_full' => $bankAccount->account_number,
                'branch_code' => $bankAccount->branch_code,
                'iban' => $bankAccount->iban,
                'verification_status' => $bankAccount->verification_status,
                'is_primary' => $bankAccount->is_primary,
                'verified_at' => $bankAccount->verified_at,
            ],
        ]);
    }

    public function setPrimaryBankAccount($id)
    {
        $bankAccount = BankAccount::where('user_id', auth()->id())->find($id);

        if (!$bankAccount) {
            return response([
                'status' => 0,
                'message' => 'Bank account not found.',
            ], 404);
        }

        if ($bankAccount->verification_status !== 'verified') {
            return response([
                'status' => 0,
                'message' => 'Only verified bank accounts can be set as primary.',
            ], 422);
        }

        // Remove primary flag from all other accounts
        BankAccount::where('user_id', auth()->id())
            ->where('id', '!=', $id)
            ->update(['is_primary' => false]);

        // Set this account as primary
        $bankAccount->update(['is_primary' => true]);

        return response([
            'status' => 1,
            'message' => 'Primary bank account set successfully.',
        ]);
    }

    // Admin Bank Account Verification APIs
    public function adminBankAccounts(Request $req)
    {
        // Only admins can access this
        if (!auth()->user()->isAdmin()) {
            return response([
                'status' => 0,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $query = BankAccount::with(['user', 'bank']);

        // Filter by status
        if ($req->has('status')) {
            $query->where('verification_status', $req->status);
        }

        // Filter by user type
        if ($req->has('user_type')) {
            $query->whereHas('user', function ($q) use ($req) {
                $q->where('user_type', $req->user_type);
            });
        }

        $bankAccounts = $query->paginate(20);

        return response([
            'status' => 1,
            'bank_accounts' => $bankAccounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'user_id' => $account->user_id,
                    'user_name' => $account->user->name,
                    'user_email' => $account->user->email,
                    'user_mobile' => $account->user->mobile,
                    'user_type' => User::roleLabel($account->user->user_type),
                    'bank_name' => $account->bank->name,
                    'account_holder_name' => $account->account_holder_name,
                    'account_number' => $account->maskAccountNumber(),
                    'iban' => $account->iban,
                    'verification_status' => $account->verification_status,
                    'verified_at' => $account->verified_at,
                    'verification_notes' => $account->verification_notes,
                    'created_at' => $account->created_at,
                ];
            }),
            'pagination' => [
                'total' => $bankAccounts->total(),
                'per_page' => $bankAccounts->perPage(),
                'current_page' => $bankAccounts->currentPage(),
                'last_page' => $bankAccounts->lastPage(),
            ],
        ]);
    }

    public function adminBankAccountDetail($id)
    {
        // Only admins can access this
        if (!auth()->user()->isAdmin()) {
            return response([
                'status' => 0,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $bankAccount = BankAccount::with(['user', 'bank', 'verifiedByUser'])->find($id);

        if (!$bankAccount) {
            return response([
                'status' => 0,
                'message' => 'Bank account not found.',
            ], 404);
        }

        return response([
            'status' => 1,
            'bank_account' => [
                'id' => $bankAccount->id,
                'user_id' => $bankAccount->user_id,
                'user_name' => $bankAccount->user->name,
                'user_email' => $bankAccount->user->email,
                'user_mobile' => $bankAccount->user->mobile,
                'user_type' => User::roleLabel($bankAccount->user->user_type),
                'bank_id' => $bankAccount->bank_id,
                'bank_name' => $bankAccount->bank->name,
                'account_holder_name' => $bankAccount->account_holder_name,
                'account_number' => $bankAccount->account_number,
                'account_number_masked' => $bankAccount->maskAccountNumber(),
                'branch_code' => $bankAccount->branch_code,
                'iban' => $bankAccount->iban,
                'verification_status' => $bankAccount->verification_status,
                'verified_by' => $bankAccount->verifiedByUser ? $bankAccount->verifiedByUser->name : null,
                'verified_at' => $bankAccount->verified_at,
                'verification_notes' => $bankAccount->verification_notes,
                'is_primary' => $bankAccount->is_primary,
                'created_at' => $bankAccount->created_at,
                'updated_at' => $bankAccount->updated_at,
            ],
        ]);
    }

    public function verifyBankAccount($id, Request $req)
    {
        // Only admins can verify
        if (!auth()->user()->isAdmin()) {
            return response([
                'status' => 0,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $data = $req->validate([
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes' => 'required|string|max:500',
        ]);

        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response([
                'status' => 0,
                'message' => 'Bank account not found.',
            ], 404);
        }

        if ($bankAccount->verification_status !== 'pending') {
            return response([
                'status' => 0,
                'message' => 'Only pending bank accounts can be verified.',
            ], 422);
        }

        $bankAccount->update([
            'verification_status' => $data['verification_status'],
            'verification_notes' => $data['verification_notes'],
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Notify user about verification result
        $message = $data['verification_status'] === 'verified'
            ? 'Your bank account has been verified successfully.'
            : 'Your bank account verification has been rejected.';

        User::storeAppNotification(
            $bankAccount->user_id,
            $message,
            'bank_accounts',
            'account_updates'
        );

        return response([
            'status' => 1,
            'message' => 'Bank account ' . $data['verification_status'] . ' successfully.',
            'bank_account' => [
                'id' => $bankAccount->id,
                'verification_status' => $bankAccount->verification_status,
                'verified_at' => $bankAccount->verified_at,
            ],
        ]);
    }

    
}

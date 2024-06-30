<?php

namespace App\Http\Controllers\Admin;

use App\Events\AppWebsocket;
use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\PaymentMethod;
use App\Models\Request as ModelRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    //
    public function createRequest(Request $req)
    {
        $req->validate([
            // 'from_date' => 'required',
            // 'to_date' => 'required',
            'parcel_lat' => 'required',
            'parcel_long' => 'required',
            'parcel_address' => 'required',
            'receiver_mobile' => 'required',
            'category_id' => 'required|int',
            'delivery_date' => 'required'
        ]);

        $images = [];
        if(isset($_FILES['images']))
        {
            // print_r($_FILES['images']); exit;
            if ($req->hasFile('images')) {
                foreach ($req->file('images') as $image) {
                    
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    // You may also store the image information in the database if needed.
                    $images[] = $imageName;
                }
    
            }
        }
        $user = auth()->user();
        $request = ModelRequest::create([
            'user_id' => $user->id,
            'from_date' => date('Y-m-d'),
            'to_date' => $req->delivery_date,
            'images' => json_encode($images),
            'parcel_lat' => $req->parcel_lat,
            'parcel_long' => $req->parcel_long,
            'parcel_address' => $req->parcel_address,
            'receiver_lat' => $req->receiver_lat,
            'receiver_long' => $req->receiver_long,
            'receiver_address' => $req->receiver_address,
            'receiver_mobile' => $req->receiver_mobile,
            'category_id' => $req->category_id
        ]);
        if($request)
        {
            $notification = new Notification();
            $notification->user_id = auth()->user()->id; // Assuming the user is authenticated
            $notification->message = 'Your Request created Successfully';
            $notification->page = 'request_page';
            $notification->save();
            $data = [];
            $data['title'] = 'New request';
            $data['body'] = 'A new request created in this region. Click here';
            $data['request_id'] = $request->id;
            $data['is_driver'] = 1;
            // User::sendNotification($data);
            $parcel_city = $req->parcel_city;
            $users = User::where('user_type', 2)
                    ->whereRaw('? LIKE CONCAT("%", city, "%")', [$parcel_city])
                    ->get();
            if(count($users) > 0)
            {
                foreach($users as $driver)
                {
                    $data['device_token'] = $driver->device_token;
                    User::sendNotification($data);
                }
            }
                return response()->json(['msg' => 'success', 'request' => $request, 'drivers' => $users]);
            
            
        } else {
            return response()->json(['msg' => 'Something went wrong']);
        }
    }

    public function sendMessage()
    {
        $channel = 'AppChannel_8';
            var_dump(event(new AppWebsocket($channel, "Request Created Successfully", 1, 0)));
    }

    // public function allTrips()
    // {
    //     $requests = ModelRequest::where('status', 0)->paginate(20);
    //     return response()->json(['all_trips' => $requests]);
    // }

    public function getRequest(Request $req)
    {
        $attrs = $req->validate([
            'id' => 'required|int'
        ]);

        $requestWithUser = ModelRequest::with('user:id,name')->find($req->id);

        return response()->json(['requestData' => $requestWithUser]);
    }
    public function rooteTimeAndDuration(Request $req)
    {
        $data = $req->validate([
            'origin' => 'required',
            'destination' => 'required'
        ]);
        // $origin =  $req->origin;     //"Gaggoo, Vehari, Punjab, Pakistan"; // You can also use latitude and longitude here
        // $destination =  $req->destination;    //"Burewala, Vehari, Punjab, Pakistan"; // You can also use latitude and longitude here

        // return $this->calculateDistanceAndTime($originLat, $originLng, $destLat, $destLng);
    }
    public function calculateDistanceAndTime($originLat, $originLng, $destLat, $destLng)//calculateDistanceAndTime($origin, $destination) //
    {
        $client = new Client();
        $response = $client->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'query' => [
                'origins' =>  $originLat.','.$originLng, //$origin,//,
                'destinations' => $destLat.','.$destLng,//$destination,//,
                'mode' => 'driving',
                'key' => env('GOOGLE_DISTANCE_MATRIX_API_KEY'),
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        // echo "<pre>";    print_r($data); exit;
        // Check if the response status is OK
        if ($data['status'] == 'OK') {
            // print_r($data); exit;
            // Extract distance in meters
            $distance = $data['rows'][0]['elements'][0]['distance']['value'];
            // echo "<pre>";    print_r($data); exit;
            // Convert distance to kilometers
            $distanceInKm = $distance / 1000;
            // echo $distanceInKm; exit;
            // Extract duration in seconds
            $duration = $data['rows'][0]['elements'][0]['duration']['value'];

            // Convert duration to minutes
            $durationInMinutes = $duration / 60;

            // Extract the estimated arrival time
            $arrivalTime = now()->addMinutes($durationInMinutes);

            return response()->json([
                'distance' => $distanceInKm, // Distance in kilometers
                'duration' => $durationInMinutes, // Duration in minutes
                'arrival_time' => $arrivalTime, // Estimated arrival time
            ]);
        } else {
            // If the response status is not OK, return an error
            return response()->json(['error' => 'Unable to calculate distance and time.']);
        }
    }
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius of the Earth in kilometers
    
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
    
        $latDifference = $lat2Rad - $lat1Rad;
        $lonDifference = $lon2Rad - $lon1Rad;
    
        $a = sin($latDifference / 2) * sin($latDifference / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($lonDifference / 2) * sin($lonDifference / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earthRadius * $c; // Distance in kilometers
    
        return $distance;
    }

    public function offerList()
    {
        
       // Get the current date
        $currentDate = Carbon::now()->format('Y-m-d');

        $requestIds = ModelRequest::where('user_id', auth()->user()->id)
            ->where('status', 0)
            ->whereDate('to_date', '>=', $currentDate)
            ->pluck('id');
        // print_r($requestIds); exit;
        $requestIds = json_decode(json_encode($requestIds), true);
        if(count($requestIds) > 0)
        {
            $offers = Offer::with([
                'request' => function($query) use ($requestIds) {
                    $query->select('id', 'user_id', 'parcel_lat', 'parcel_long', 'parcel_address', 'receiver_lat', 'receiver_long', 'receiver_address', 'from_date', 'to_date')
                        ->whereIn('id', $requestIds); // Filter the requests by specified IDs
                    // If you want to include user data related to the request, uncomment the following:
                    // ->with(['user' => function($query) {
                    //     $query->select('id', 'name', 'email', 'mobile');  // Specify columns for the user related to the request
                    // }]);
                },
                'user' => function($query) {
                    $query->select('id', 'name', 'email', 'mobile', 'latitude', 'longitude', 'street_address');
                }
            ])->whereIn('request_id', $requestIds)->get();
                // print_r($offers); exit;
            if(count($offers) > 0)
            {
                foreach($offers as $key => $offer)
                {
    
                    // $offers[$key]['data'] = $this->calculateDistanceAndTime($offer->request->parcel_lat,$offer->request->parcel_long, $offer->user->latitude, $offer->user->longitude);
                }
                return response()->json([
                    'offers' => $offers
                ]);
            } else {
                return response()->json([
                    'msg' => 'No Offer found'
                ]);
            }
            
        } else {
            return response()->json([
                'msg' => 'No Offer found'
            ]);
        }
        
    }

    
    public function acceptOffer(Request $req)
    {
        $data = $req->validate([
            'amount' => 'required',
            'request_id' => 'required',
            'offer_id' => 'required',
            'payment_method' => 'required',
            'description' => 'required'
        ]);

        $pm = PaymentMethod::where('id', $req->payment_method)->first();
        // print_r($pm);
        if($pm->slug == 'click_pay')
        {
            $data['profile_key'] = $pm->public_key;
            $data['secret_key'] = $pm->secret_key;
    
            $payment = Offer::clickPay($data);
            $payment = json_decode($payment, true);
            // print_r($payment); exit;
            if(isset($payment['invoice_id']))
            {
                $request = DB::table('requests')->where('id', $req->request_id)->update([
                    'invoice_id' => $payment['invoice_id'],
                    'amount' => $req->amount
                ]);
                if($request)
                {
                    $offer = Offer::find($req->offer_id);
                    $driver = User::find($offer->user_id);
                    $notification = new Notification();
                    $notification->user_id = $offer->user_id; // Assuming the user is authenticated
                    $notification->message = 'Your offer accepted against the request ID : '.$req->request_id;
                    $notification->page = 'request_page';
                    $notification->save();
                    $data = [];
                    $data['title'] = 'Accept Offer';
                    $data['body'] = 'Your offer accepted against the request ID : '.$req->request_id;
                    $data['device_token'] = $driver->device_token;
                    $data['is_driver'] = 1;
                    $data['request_id'] = $req->request_id;
                    
                    $res = User::sendNotification($data);
                    return response()->json(['data' => $payment, 'pn_status' => $res]);
                } else {
                    return response()->json(['msg' => "Update method fails"]);
                }
            } else {
                return response()->json(['msg' => "Something Wrong in request."]);
            }
        } elseif($pm->slug == 'COD') {
            // echo "success"; exit;
            $wdata['code'] = $req->request_id."|".generateRandomCode();
            $request = ModelRequest::where('id', $req->request_id)->update([
                'offer_id' => $req->offer_id,
                'amount' => $req->amount,
                'status' => 1,
                'code' => $wdata['code']
            ]);
            if($request)
                {
                    $user_req = ModelRequest::where('id', $req->request_id)->first();
                    send_message($wdata, $user_req->receiver_mobile);
                    $offer = Offer::find($req->offer_id);
                    Offer::where('id', $req->offer_id)->update(['is_accept' => 1]);
                    $driver = User::find($offer->user_id);
                    $notification = new Notification();
                    $notification->user_id = $offer->user_id; // Assuming the user is authenticated
                    $notification->message = 'Your offer accepted against the request ID : '.$req->request_id;
                    $notification->page = 'request_page';
                    $notification->save();
                    $data = [];
                    $data['title'] = 'Accept Offer';
                    $data['body'] = 'Your offer accepted against the request ID : '.$req->request_id;
                    $data['device_token'] = $driver->device_token;
                    $data['request_id'] = $req->request_id;
                    $data['is_driver'] = 1;
                    
                    $res = User::sendNotification($data);
                    return response()->json(['data' => [
                        'msg' => 'Request accepted successfully (COD)', 'pn_status' => $res
                    ]]);
                } else {
                    return response()->json(['msg' => "Update method fails"]);
                }
        } elseif($pm->slug == 'wallet')
        {
            $wallet = Wallet::where('user_id', auth()->user()->id)->first();

            if($wallet->amount >= $req->amount )
            {
                DB::beginTransaction();
                try{
                    $request = DB::table('requests')->where('id', $req->request_id)->update([
                        'offer_id' => $req->offer_id,
                        'amount' => $req->amount,
                        'payment_status' => 1
                    ]);
                        if($request)
                        {
                            
                            $diff = doubleval($wallet->amount - $req->amount);
                            $wUpdate = Wallet::where('user_id', auth()->user()->id)->update([
                                'amount' => $diff
                            ]);
                            if($wUpdate)
                            {
                                $history = WalletHistory::create([
                                    'wallet_id' => $wallet->id,
                                    'amount' => $req->amount,
                                    'is_expanse' => 1,
                                    'description' => 'Charge your wallet against your parcel request ('.$req->request_id.') with amount '.$req->amount
                                ]);
                                if($history)
                                {
                                    
                                    $offer = Offer::find($req->offer_id);
                                    $driver = User::find($offer->user_id);
                                    $notification = new Notification();
                                    $notification->user_id = $offer->user_id; // Assuming the user is authenticated
                                    $notification->message = 'Your offer accepted against the request ID : '.$req->request_id;
                                    $notification->page = 'request_page';
                                    $notification->save();
                                    DB::commit();
                                    $data = [];
                                    $data['title'] = 'Accept Offer';
                                    $data['body'] = 'Your offer accepted against the request ID : '.$req->request_id;
                                    $data['device_token'] = $driver->device_token;
                                    $data['is_driver'] = 1;
                                    $data['request_id'] = $req->request_id;
                                    $res = User::sendNotification($data);
                                    return response()->json(['data' => [
                                        'msg' => 'request accepted successfully', 'pn_status' => $res
                                    ]]);
                                }
                            } else {
                                return response()->json(['data' => [
                                    'msg' => 'Wallet charge request faild'
                                ]]);
                            }
                        } else {
                            return response()->json(['msg' => "Update method fails"]);
                        }
                        
                }  catch (\Exception $e) {
                    // Something went wrong, roll back the transaction
                    DB::rollBack();
                }
                
            } else {
                return response()->json(['msg' => 'User have not enough amount in wallet']);
            }
        }
        



    }

    public function getRequestData($id)
    {
        $request = ModelRequest::find($id);

        return response()->json(['data' => $request]);
    }
    public function allTrips()
    {
        $user = auth()->user();

        $requests = ModelRequest::with('user')->where('status', 0)
        ->where('parcel_address', 'like', '%' . $user->city . '%')->paginate(10);

        return response()->json(['data' => $requests]);
    }

    public function markCompleteRequest(Request $req)
    {
        // print_r(auth()->user()); exit;
        $req->validate([
            'code' => 'required'
        ]);
        $request = ModelRequest::where('code',$req->code)->first();
        

        // print_r($update); exit;
        if($request)
        {
            
            if(isset($request->payment_status) && $request->payment_status == 1)
            {
                $update = ModelRequest::where('code', $req->code)->update(['status' => 3]);
                $offer = Offer::where('is_accept', 1)->find($request->offer_id);
                if (is_null($offer) || is_null($offer->id)) {
                    return response()->json(['msg' => 'Offer not found']);
                }
                $wallet = Wallet::where('user_id', $offer->user_id)->first();
                $amount = $wallet->amount+$request->amount;
                $walletUpdate = DB::table('wallets')->where('id', $wallet->id)->update([
                    'amount' => $amount
                ]);
                if($walletUpdate)
                {
                    $wallet_history = WalletHistory::create([
                        'wallet_id' => $wallet->id,
                        'amount' => $request->amount,
                        'is_deposite' => 1,
                        'description' => 'Payment of ride which ID is'.$req->request_id,
                    ]);
                    if($wallet_history)
                    {
                        return response()->json(['msg' => 'Request status update successfully']);
                    }  else {
                        return response()->json(['msg' => 'History not created of current request']);
                    }
                }  else {
                    return response()->json(['msg' => 'Wallet updation faild']);
                }
            }
        }  else {
            return response()->json(['msg' => 'Code does not match']);
        }
    }

    public function parcelConfirmationApi(Request $req)
    {
        $req->validate([
            'code' => 'code'
        ]);

        $confirm = Offer::with('request')->where('user_id', auth()->user()->id)->where('request_id', $req->request_id)->where('is_accept', 1)->first();
        if($confirm && $confirm->id > 0)
        {
            return response()->json([
                'data' => $confirm,
                'msg' => 'Request confirm successfully'
            ]);
        } else {
            return response()->json([
                'msg' => 'Request confirmation failed'
            ]);
        }
    }

    public function receiverAddressUpdate(Request $req)
    {
        $req->validate([
            'request_id' => 'required|int',
            'receiver_lat' => 'required',
            'receiver_long' => 'required',
            'receiver_address' => 'required'
        ]);

        $update = ModelRequest::where('id', $req->request_id)->update($req);
        if($update)
        {
            return response()->json(['msg' => 'Receiver address update successfully']);
        } else {
            return response()->json(['msg' => 'Receiver address update faild']);
        }
    }
    public function near_by_drivers(Request $req)
    {
        $user = auth()->user();

        $users = User::where('user_type', 2)->where('status', 1)
        ->where('city', 'like', '%' . $user->city . '%')->get();

        return response()->json(['data' => $users]);
    }
    public function tracking(Request $req)
    {
        $req->validate([
            'request_id' => 'required|int'
        ]);

        $request = ModelRequest::find($req->request_id);
        $latestHistory = History::where('request_id', $req->request_id)->latest()->first();
        $data = [];
        $request->driver_current_record = $latestHistory;

        return response()->json(['data' => $request]);
    }

    function test()
    {
        $data = [];
        $data['title'] = 'Parcel Collected';
        $data['body'] = 'Your parcel on the way, click to track your parcel';
        $data['request_id'] = '123';
        // $data['is_driver'] = 1;
        // $data['device_token'] = 'dN-4DUh1TamgfSsYKPvjM0:APA91bEOO5VxmPUDrI4kskY-LF7btvIoToiHEJ5mNYPd3SGU6ESsgcKD7oCCSXaFpeUSC27NPbZ8xSjPE6BsLScCSQjyVy6Dv0Ltp-PFDob_wGtGyt1PkVo6gnf6UsZKOAm1LAvBuwri';
        $data['device_token'] = 'fYLZGa1fTbW6m_ptgKhBKl:APA91bFxhZpdcCJdAaVCkMx1OXR7OaMVgt3Idw7kdFHIbVvckynsOA2R1d96h7n1uGO0E3ucJs24nXHD342GOP1Aa0CH6Ldk8MI9vYslUYTa0p40CK1O7sewOqJnqY1rO4IY7nXtp3XQ';
        $response = User::sendNotification($data);

        print_r($response);
    }
}



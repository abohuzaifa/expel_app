<?php

namespace App\Http\Controllers\Admin;

use App\Events\AppWebsocket;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\PaymentMethod;
use App\Models\Request as ModelRequest;
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
            'from_date' => 'required',
            'to_date' => 'required',
            'parcel_lat' => 'required',
            'parcel_long' => 'required',
            'parcel_address' => 'required',
            'receiver_mobile' => 'required'
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
            'from_date' => $req->from_date,
            'to_date' => $req->to_date,
            'images' => json_encode($images),
            'parcel_lat' => $req->parcel_lat,
            'parcel_long' => $req->parcel_long,
            'parcel_address' => $req->parcel_address,
            'receiver_lat' => $req->receiver_lat,
            'receiver_long' => $req->receiver_long,
            'receiver_address' => $req->receiver_address,
            'receiver_mobile' => $req->receiver_mobile
        ]);
        if($request)
        {
            $channel = 'AppChannel_'.$request->id;
            // event(new AppWebsocket($channel, "Request Created Successfully", $user->id, 0));
           
                DB::table('requests')->where('id', $request->id)->update(['channel_name' => $channel]);
                $request->channel_name = $channel;
                return response()->json(['msg' => 'success', 'request' => $request]);
            
            
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

        return $this->calculateDistanceAndTime($originLat, $originLng, $destLat, $destLng);
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
            print_r($data); exit;
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
        
        $requestIds = ModelRequest::where('user_id', auth()->user()->id)->where('status', 0)->pluck('id');
        // print_r($requestIds); exit;
        $requestIds = json_decode(json_encode($requestIds), true);
        if(count($requestIds) > 0)
        {
            $offers = Offer::with([
                'request' => function($query) use ($requestIds) {
                    $query->select('id', 'user_id', 'parcel_lat', 'parcel_long', 'parcel_address')
                        ->whereIn('id', $requestIds); // Filter the requests by specified IDs
                    // If you want to include user data related to the request, uncomment the following:
                    // ->with(['user' => function($query) {
                    //     $query->select('id', 'name', 'email', 'mobile');  // Specify columns for the user related to the request
                    // }]);
                },
                'user' => function($query) {
                    $query->select('id', 'name', 'email', 'mobile', 'latitude', 'longitude', 'street_address');
                }
            ])->get();
    
            if(count($offers) > 0)
            {
                foreach($offers as $key => $offer)
                {
    
                    $offers[$key]['data'] = $this->calculateDistanceAndTime($offer->request->parcel_lat,$offer->request->parcel_long, $offer->user->latitude, $offer->user->longitude);
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
            'card' => 'required',
            'description' => 'required'
        ]);

        $pm = PaymentMethod::where('slug', 'click_pay')->first();
        // print_r($pm);
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
                return response()->json(['data' => $payment]);
            } else {
                return response()->json(['msg' => "Update method fails"]);
            }
        } else {
            return response()->json(['msg' => "Something Wrong in request."]);
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

        $requests = ModelRequest::with('user')->paginate(10);

        return response()->json(['data' => $requests]);
    }
}



<?php

namespace App\Http\Controllers\Admin;

use App\Events\AppWebsocket;
use App\Http\Controllers\Controller;
use App\Models\Request as ModelRequest;
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

        $user = auth()->user();
        $request = ModelRequest::create([
            'user_id' => $user->id,
            'from_date' => $req->from_date,
            'to_date' => $req->to_date,
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
            event(new AppWebsocket($channel, "Request Created Successfully", $user->id, 0));
           
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
}

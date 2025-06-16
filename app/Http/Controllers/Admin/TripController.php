<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    //
    public function addTrip(Request $req)
    {
        $attrs = $req->validate([
            'from' => 'required',
            'to' => 'required',
            'date' => 'required|date',
            'time' => 'required',
             'description' => 'nullable|string',
            //  'price' => 'required|int'
        ]);
        $user = auth()->user();

        $trip = Trip::create([
            'from' => $req->from,
            'to' => $req->to,
            'date' => $req->date,
            'time' => $req->time,
            'user_id' => $user->id,
            'price' => $req->price ?? 0,
            'description' => $req->description
        ]);

        return response()->json([
            'msg'=>'success',
            'trip' => $trip
        ]);
    }
    
    function cities()
    {
        $cities = City::all();
        return response()->json($cities);
    }

    function tripList()
    {
        $user = auth()->user();
        $res = [];
        if($user->user_type == 1)
        {
            $trips = $this->getUpcomingTrips();
            foreach($trips as $trip)
            {
                $trip->from = City::find($trip->from);
                $trip->to = City::find($trip->to);
                $res[] = $trip;
            }
        } else {
            $trips = Trip::where('user_id', $user->id)->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `time`), '%Y-%m-%d %H:%i:%s') >= ?", [now()])
                ->orderByRaw("STR_TO_DATE(CONCAT(`date`, ' ', `time`), '%Y-%m-%d %H:%i:%s')")
                ->get();
            foreach($trips as $trip)
            {
                $trip->from = City::find($trip->from);
                $trip->to = City::find($trip->to);
                $res[] = $trip;
            }
        }
        
        return response()->json($res);
    }
    public function getUpcomingTrips()
    {
        // Combine date and time fields for comparison
        return Trip::whereRaw("STR_TO_DATE(CONCAT(`date`, ' ', `time`), '%Y-%m-%d %H:%i:%s') >= ?", [now()])
                ->orderByRaw("STR_TO_DATE(CONCAT(`date`, ' ', `time`), '%Y-%m-%d %H:%i:%s')")
                ->get();
    }
}

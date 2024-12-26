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
        ]);
        $user = auth()->user();

        $trip = Trip::create([
            'from' => $req->from,
            'to' => $req->to,
            'date' => $req->date,
            'time' => $req->time,
            'user_id' => $user->id,
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
}

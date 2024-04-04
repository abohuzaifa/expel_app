<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'time' => 'required'
        ]);
        $user = auth()->user();

        $trip = Trip::create([
            'from' => $req->from,
            'to' => $req->to,
            'date' => $req->date,
            'time' => $req->time,
            'user_id' => $user->id
        ]);

        return response()->json([
            'msg'=>'success',
            'trip' => $trip
        ]);
    }
}

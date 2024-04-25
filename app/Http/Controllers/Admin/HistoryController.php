<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    //
    public function createHistory(Request $req)
    {
        $data = $req->validate([
            'lat' => 'required',
            'long' => 'required',
            'address' => 'required',
            'is_start' => 'required|int',
            'is_end' => 'required|int',
            'request_id' => 'required|int'
        ]);

        $history = History::create([
            'request_id' => $req->request_id,
            'lat' => $req->lat,
            'long' => $req->long,
            'address' => $req->address,
            'is_start' => $req->is_start,
            'is_end' => $req->is_end,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json(['msg' => 'success', 'data' => $history]);
    }

    public function trackParcel(Request $req)
    {
        $data = $req->validate([
            'request_id' => 'required'
        ]);

        $lastRecord = History::with('request', 'request.user')->where('request_id', $req->request_id)->latest()->first();

        if ($lastRecord) {
            return response()->json(['data' => $lastRecord]);
        } else {
            return response()->json(['msg' => 'No record found against this request']);
        }
    }

    
}

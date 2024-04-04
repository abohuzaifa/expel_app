<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    //
    public function addOffer(Request $req)
    {
        $attrs = $req->validate([
            'request_id' => 'required',
            'amount' => 'required'
        ]);

        $offer = Offer::create([
            'request_id' => $req->request_id,
            'amount' => $req->amount,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'msg' => 'success',
            'offer' => $offer
        ]);
    }
} 

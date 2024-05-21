<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Request as ModelsRequest;
use App\Models\User;
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
        $driver = auth()->user();
        if($offer)
        {
            $request = ModelsRequest::find($req->request_id);
            $user = User::find($request->user_id);
                $notification = new Notification();
                $notification->user_id = $request->user_id; // Assuming the user is authenticated
                $notification->message = $driver->name.' add new offer againest your request';
                $notification->page = 'request_page';
                $notification->save();
                $data = [];
                $data['title'] = 'New Offer';
                $data['body'] = $driver->name.' add new offer againest your request';
                $data['device_token'] = $user->device_token;
                $data['is_driver'] = 0;
                User::sendNotification($data);
                return response()->json([
                    'msg' => 'success',
                    'offer' => $offer
                ]);
        } else {
            return response()->json([
                'msg' => 'failed',
            ]);
        }
        
    }
} 

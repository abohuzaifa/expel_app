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
                User::storeAppNotification(
                    $request->user_id,
                    $driver->name.' add new offer againest your request',
                    'request_page',
                    'new_offers'
                );
                $data = [];
                $data['title'] = 'New Offer';
                $data['body'] = $driver->name.' add new offer againest your request';
                $data['device_token'] = $user->device_token;
                $data['request_id'] = $req->request_id;
                $data['is_driver'] = 0;
                $data['user_id'] = $user->id;
                $data['setting_key'] = 'new_offers';
                // print_r($user); print_r($driver->device_token);exit;
                $res = User::sendNotification($data);
                return response()->json([
                    'msg' => 'success',
                    'offer' => $offer,
                    'pn_status' => $res
                ]);
        } else {
            return response()->json([
                'msg' => 'failed',
            ]);
        }
        
    }

    public function declineOffer(Request $req)
    {
        $req->validate([
            'offer_id' => 'required|int'
        ]);

        $offer = Offer::where('id', $req->offer_id)->update([
            'is_reject' => 1
        ]);

        return response()->json(['msg' => 'Offer cancelled successfully']);
    }
} 

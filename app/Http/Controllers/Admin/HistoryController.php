<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Notification;
use App\Models\Request as ModelsRequest;
use App\Models\User;
use Carbon\Carbon;
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
        $user = auth()->user();
        $request = ModelsRequest::find($data['request_id']);
        $request_user = User::find($request->user_id);
        if($req->is_start == 1)
        {
            $notification = new Notification();
            $notification->user_id = $request->user_id; // Assuming the user is authenticated
            $notification->message = $user->name.'('.$user->number_plate.') collected the parcel, your parcel on the way, click to track your parcel';
            $notification->page = 'track_parcel';
            $notification->save();

            $data = [];
            $data['title'] = 'Parcel Collected';
            $data['body'] = $user->name.' collected the parcel, your parcel on the way, click to track your parcel';
            $data['request_id'] = $req->request_id;
            $data['device_token'] = $request_user->device_token;
            $res = User::sendNotification($data);
        } else if($req->is_end == 1)
        {
            $notification = new Notification();
            $notification->user_id = $request->user_id; // Assuming the user is authenticated
            $notification->message = 'Your parcel reched your destination, click to track your parcel';
            $notification->page = 'track_parcel';
            $notification->save();

            $data = [];
            $data['title'] = 'Parcel Collected';
            $data['body'] = 'Your parcel reched your destination, click to track your parcel';
            $data['request_id'] = $req->request_id;
            $data['device_token'] = $request_user->device_token;
            $res = User::sendNotification($data);
        } else {
            $notification = new Notification();
            $notification->user_id = $request->user_id; // Assuming the user is authenticated
            $notification->message = 'Your parcel on the way, click to track your parcel';
            $notification->page = 'track_parcel';
            $notification->save();

            $data = [];
            $data['title'] = 'Parcel Collected';
            $data['body'] = 'Your parcel on the way, click to track your parcel';
            $data['request_id'] = $req->request_id;
            $data['device_token'] = $request_user->device_token;
            $res = User::sendNotification($data);
        }
        
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

    public function notificationList()
    {
        $user = auth()->user(); // $userId is the ID of the user you want to retrieve notifications for

        if ($user) {
            $notifications = $user->notifications()
                ->where('created_at', '>=', Carbon::now()->subMonth()) // Notifications from one month ago until now
                ->orderBy('created_at', 'desc')
                ->get();
            if(count($notifications) > 0)
            {
                return response()->json([
                    'data' => $notifications
                ]);
            } else {
                return response()->json([
                    'data' => []
                ]);
            }
            // $notifications now contains the notifications in descending order of creation
        } else {
            // User not found
        }
    }
    
}

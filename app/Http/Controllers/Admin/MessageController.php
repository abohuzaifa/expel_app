<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Message;
use App\Models\Chat;
use App\Models\Offer;
use App\Models\Request as ModelsRequest;
use App\Models\User;

class MessageController extends Controller
{
    //
    public function index()
    {
        return Message::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => 'required',
            'request_id' => 'required|int',
            'is_driver' => 'required|int',
            'is_user' => 'required|int'
        ]);

        $data['user_id'] = auth()->user()->id;
        $message = Message::create($data);
        if($message)
        {
            $data = [];
            $data['title'] = 'New Message';
            $data['body'] = $request->text;
            $data['request_id'] = $request->request_id;
            if($request->is_driver == 1)
            {
                $data['is_driver'] = 1;
                $req = ModelsRequest::where('id', $request->request_id)->first();
                $offer = Offer::find($req->offer_id);
                $user = User::find($offer->user_id);
                $data['device_token'] = $user->device_token;
            } else {
                $data['is_driver'] = 0;
                $req = ModelsRequest::where('id', $request->request_id)->first();
                // $offer = Offer::find($req->offer_id);
                $user = User::find($req->user_id);
                $data['device_token'] = $user->device_token;
            }
            
            User::sendNotification($data);
            return response()->json($message, 200);
        }
    }

    public function show(Message $subCategory)
    {
        return $subCategory;
    }

    public function update(Request $request, Message $subCategory)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|nullable|string|max:255'
        ]);

        $subCategory->update($request->all());
        return response()->json($subCategory, 200);
    }

    public function destroy(Message $subCategory)
    {
        $subCategory->delete();
        return response()->json(null, 200);
    }
}

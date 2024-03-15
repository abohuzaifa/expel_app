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
use App\Models\User;

class MessageController extends Controller
{
    //
    public function sendMessage(Request $request)
    {
        $attrs = $request->validate([
            "shop_id" => "required|int",
        ]);

        $user = auth()->user();
        $chat = DB::table("chats")->where("shop_id", $attrs['shop_id'])->where("user_id", $user->id)->get();
        if(count($chat) > 0)
        {
            $chat_id = $chat[0]->id;
        } else {
            $chat = Chat::create([
                "shop_id" => $attrs['shop_id'],
                "user_id" => $user->id
            ]);
            // print_r($chat->id);
            $chat_id = $chat->id;
        }
        $images = [];
        if(isset($_FILES['images']))
        {
            // print_r($_FILES['images']); exit;
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {

                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    // You may also store the image information in the database if needed.
                    $images[] = $imageName;
                }
            
            }
        }
        $message = Message::create([
            "shop_id" => $attrs['shop_id'],
            "user_id" => $user->id,
            "text" => $request->text,
            "is_user" => $user->user_type == 2 ? 1 : 0,
            "is_shop" => $user->user_type == 2 ? 0 : 1,
            "chat_id" => $chat_id,
            "images" => json_encode($images)
        ]);
        $message->image_base_url = asset('/images');
        if($message)
        {
            return response([
                "status" => "1",
                "msg" => json_decode(json_encode($message), true)
            ]);
        } else {
            return response([
                "stataus" => "0",
                "message" => "Something Went Wrong"
            ]);
        }
    }

    public function chatMessages(Request $request)
    {
        $attrs = $request->validate([
            "shop_id" => "required|int"
        ]);

        $user = auth()->user();
        if($user->user_type == 2)
        {
            $chat = DB::table("chats")->where("shop_id", $attrs['shop_id'])->where("user_id", $user->id)->get();
            if(count($chat) > 0)
            {
                $chat_id = $chat[0]->id;
                DB::select("UPDATE messages SET is_read=1 WHERE chat_id=:chid AND is_shop=1", [':chid' => $chat_id]);
                $messages = DB::table("messages")->where("chat_id","=",$chat_id)->where("user_id","=",$user->id)->orderByDesc("id")->paginate(30);
                
                    return response([
                        "status" => "1",
                        "msgs" => json_decode(json_encode($messages), true)
                    ]); 
            }
        } else {
            $chat = DB::table("chats")->where("shop_id", $attrs['shop_id'])->where("user_id", $user->id)->get();
            if(count($chat) > 0)
            {
                $chat_id = $chat[0]->id;
                DB::select("UPDATE messages SET is_read=1 WHERE chat_id=:chid AND is_user=1", [':chid' => $chat_id]);
                $messages = DB::table("messages")->where("chat_id","=",$chat_id)->where("user_id","=",$user->id)->orderByDesc("id")->paginate(30);
                
                    return response([
                        "status" => "1",
                        "msgs" => json_decode(json_encode($messages), true)
                    ]); 
            }
        }
    }

    public function allShopChatNotifications($shop_id)
    {
        $chats = DB::table("chats")->where("shop_id","=", $shop_id)->orderByDesc("id")->get();
        if(count($chats) > 0)
        {
            foreach($chats as $key => $chat)
            {
                $unread_comment = DB::select("SELECT COUNT(*) as unread FROM messages WHERE shop_id=:sid AND is_user=1 AND chat_id=:chid", [':sid' => $shop_id, ':chid' => $chat->id]);
                $chats[$key]->unread_messages = $unread_comment[0]->unread;
            }
        }
        return response([
            "status" => "1",
            "chats" => json_decode(json_encode($chats), true)
        ]);
    }

    public function allUserChatNotifications($user_id)
    {
        $chats = DB::table("chats")->where("user_id","=", $user_id)->orderByDesc("id")->get();
        if(count($chats) > 0)
        {
            foreach($chats as $key => $chat)
            {
                $unread_comment = DB::select("SELECT COUNT(*) as unread FROM messages WHERE user_id=:uid AND is_shop=1 AND chat_id=:chid", [':uid' => $user_id, ':chid' => $chat->id]);
                $chats[$key]->unread_messages = $unread_comment[0]->unread;
            }
        }
        return response([
            "status" => "1",
            "chats" => json_decode(json_encode($chats), true)
        ]);
    }

    public function getFullChatByChatID($chat_id)
    {
        $chats = DB::table("chats")->where("id","=", $chat_id)->orderByDesc("id")->get();
        $user = auth()->user();
        if($user->user_type == 2)
        {
            DB::select("UPDATE messages SET is_read=1 WHERE chat_id=:chid AND is_shop=1", [':chid' => $chat_id]);
        } else {
            DB::select("UPDATE messages SET is_read=1 WHERE chat_id=:chid AND is_user=1", [':chid' => $chat_id]);
        }
        if(count($chats) > 0)
        {
            foreach($chats as $key => $chat)
            {
                $comments = DB::select("SELECT * FROM messages WHERE chat_id=:chid ORDER BY id DESC", [':chid' => $chat->id]);
                $chats[$key]->messahes = $comments;
            }
        }
        return response([
            "status" => "1",
            "chats" => json_decode(json_encode($chats[0]), true)
        ]);
    }


}

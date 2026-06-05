<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    //
    public function index()
    {
        return view('notifications.index');
    }

    public function edit($id)
    {
        // Handle "read all" for both users table and notifications table
        if($id == 'all')
        {
            // Mark all new users as read
            DB::table('users')->where('is_read', 0)->update(['is_read' => 1]);
            
            // Mark all app notifications as read for current user
            Notification::where('is_read', 0)
                ->where('user_id', auth()->id())
                ->update(['is_read' => 1]);
                
            if(isset($_GET['choice']))
            {
                return redirect()->back();
            }
        } else {
            // Check if this is a notification ID (from notifications table)
            $notification = Notification::find($id);
            if ($notification) {
                $notification->update(['is_read' => 1]);
            } else {
                // Fallback to users table
                DB::table('users')->where('id', $id)->update(['is_read' => 1]);
            }
            
            if(isset($_GET['choice']))
            {
                return redirect()->back();
            }
        }
        
        return redirect()->route('users.index');
    }
}

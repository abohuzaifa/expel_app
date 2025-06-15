<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserNotificationSetting;
use Illuminate\Support\Facades\Auth;

class NotificationSettingController extends Controller
{
    public function show()
    {
        $setting = UserNotificationSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            [] // defaults
        );

        return response()->json($setting);
    }

    public function update(Request $request)
    {
        $data = $request->only([
            'trip_alert',
            'new_offers',
            'announcements',
            'account_updates',
            'messages',
        ]);

        $setting = UserNotificationSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return response()->json([
            'message' => 'Notification settings updated successfully.',
            'data' => $setting
        ]);
    }
}


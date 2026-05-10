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
            UserNotificationSetting::getDefaultSettings()
        );

        return response()->json([
            'status' => 1,
            'data' => $setting,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'trip_alert' => 'sometimes|boolean',
            'new_offers' => 'sometimes|boolean',
            'announcements' => 'sometimes|boolean',
            'account_updates' => 'sometimes|boolean',
            'messages' => 'sometimes|boolean',
        ]);

        $setting = UserNotificationSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return response()->json([
            'status' => 1,
            'message' => 'Notification settings updated successfully.',
            'data' => $setting
        ]);
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationApiController extends Controller
{
    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        try {
            $updated = DB::table('notifications')
                        ->where('id', $id)
                        ->update(['is_read' => 1]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $updated = DB::table('notifications')
                        ->where('is_read', 0)
                        ->update(['is_read' => 1]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} notifications marked as read"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
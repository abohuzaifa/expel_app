<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'trip_alert',
    'new_offers',
    'announcements',
    'account_updates',
    'messages',
];
    protected $casts = [
        'trip_alert' => 'boolean',
        'new_offers' => 'boolean',
        'announcements' => 'boolean',
        'account_updates' => 'boolean',
        'messages' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function getDefaultSettings()
    {
        return [
            'trip_alert' => true,
            'new_offers' => true,
            'announcements' => true,
            'account_updates' => true,
            'messages' => true,
        ];
    }
    public static function createDefaultSettings($userId)
    {
        return self::create([
            'user_id' => $userId,
            'trip_alert' => true,
            'new_offers' => true,
            'announcements' => true,
            'account_updates' => true,
            'messages' => true,
        ]);
    }
    public function updateSettings(array $settings)
    {
        $this->update($settings);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_ar',
        'email',
        'image',
        'mobile',
        'user_type',
        'password',
        'status',
        'address',
        'country',
        'otp',
        'category_id',
        'driving_license',
        'bank_id',
        'bank_account',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'otp'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public static function sendNotification($data)
    {
        $deviceToken = $data['device_token'];
        $title = $data['title'];
        $body = $data['body'];
        // $subtitle = $data['subtitle'];
        $serverKey = $data['is_driver'] == 1 ? env('DRIVER_SERVER_KEY') : env('USER_SERVER_KEY');  // Assuming server key is sent in request for simplicity

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        ];

        $notification = [
            'title' => $title,
            'body' => $body,
            // 'subtitle' => $subtitle,
            'key' => $serverKey
        ];

        $fields = [
            'to' => $deviceToken,
            'notification' => $notification,
            'priority' => 'high'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return response()->json([
            'message' => 'Notification sent successfully',
            'result' => json_decode($result, true)
        ]);
    }
}

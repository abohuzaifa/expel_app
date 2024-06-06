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
        // $deviceToken = $data['device_token'];
        // $title = $data['title'];
        // $body = $data['body'];
        // // $subtitle = $data['subtitle'];
        // $serverKey = $data['is_driver'] == 1 ? env('DRIVER_SERVER_KEY') : env('USER_SERVER_KEY');  // Assuming server key is sent in request for simplicity

        $url = 'https://fcm.googleapis.com/v1/projects/cargo-delivery-app-4fbb8/messages:send';

        $headers = [
            'Authorization: Bearer ya29.a0AXooCgt2FKRORoQjbJQMLIAQGKJzUrSyZw9qaJeolF-yG3s8W75jFQQZn-yEqlzmOVm5GB1zewZUBbl3Q6OCs_2pPFw10mK54_osnEoGzmLdcEYyhn4OPc5QlaXmADtuEUJaLWmQW42MMVsZmS_2WGCs2Um1Du4yPrJIaCgYKAfwSARESFQHGX2MiWzN-N6UDQvntLHUGW0LmNw0171',
            'Content-Type: application/json'
        ];

        // $notification = [
        //     'title' => $title,
        //     'body' => $body,
        //     // 'subtitle' => $subtitle,
        //     'key' => $serverKey
        // ];

        // $fields = [
        //     'to' => $deviceToken,
        //     'notification' => $notification,
        //     'priority' => 'high'
        // ];
//dN-4DUh1TamgfSsYKPvjM0:APA91bEOO5VxmPUDrI4kskY-LF7btvIoToiHEJ5mNYPd3SGU6ESsgcKD7oCCSXaFpeUSC27NPbZ8xSjPE6BsLScCSQjyVy6Dv0Ltp-PFDob_wGtGyt1PkVo6gnf6UsZKOAm1LAvBuwri
        $fields = '{
            "message": {
                 "token":"'.$data['device_token'].'",
                 "notification":{
                     "title":"'.$data['title'].'",
                     "body":"'.$data['body'].'"
                 },
                 "data": {
                    "request_id": "'.$data['request_id'].'"
                }
             }
         }';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
    }
}

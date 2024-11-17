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
        'device_token',
        'is_available'
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
        if(isset($data['is_driver']) && $data['is_driver'] == 1)
        {
            $url = 'https://fcm.googleapis.com/v1/projects/'.getenv('DRIVER_PROJECT_ID').'/messages:send';

            // Set your client credentials and refresh token
            $client_id = getenv('GOOGLE_CLIENT_ID_d');
            $client_secret = getenv('GOOGLE_CLIENT_SECRET_d');
            $refresh_token = getenv('DRIVER_REFRESH_TOKEN'); // Replace with your actual refresh token
        } else {
            $url = 'https://fcm.googleapis.com/v1/projects/'.getenv('DELIVERY_PROJECT_ID').'/messages:send';
    
            // Set your client credentials and refresh token
            $client_id = getenv('GOOGLE_CLIENT_ID');
            $client_secret = getenv('GOOGLE_CLIENT_SECRET');
            $refresh_token = getenv('DELIVERY_REFRESH_TOKEN'); // Replace with your actual refresh token

        }
        // echo $url; exit;
        $token_url = 'https://oauth2.googleapis.com/token';

        // Prepare the POST fields
        $post_fields = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        // Execute the cURL request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            die('Curl error: ' . curl_error($ch));
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $response_data = json_decode($response, true);
        // print_r($response_data); exit;
        // Check if the response contains an error
        if (isset($response_data['error'])) {
            die('Error refreshing the token: ' . $response_data['error']);
        }

        // print_r($response_data); exit;
        // Extract the new access token
        $newAccessToken = $response_data['access_token'];
        $headers = [
            'Authorization: Bearer '.$newAccessToken,
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
    
        // print_r($ch); exit;
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
    }

    // public static function sendNotification($data)
    // {
        
    //         $url = 'https://fcm.googleapis.com/v1/projects/opatra-d5bda/messages:send';

    //         // Set your client credentials and refresh token
    //         $client_id = getenv('GOOGLE_CLIENT_ID_junaid');
    //         $client_secret = getenv('GOOGLE_CLIENT_SECRET_junaid');
    //         $refresh_token = getenv('DRIVER_REFRESH_TOKEN_junaid'); // Replace with your actual refresh token
        
    //     // echo $url; exit;
    //     $token_url = 'https://oauth2.googleapis.com/token';

    //     // Prepare the POST fields
    //     $post_fields = [
    //         'client_id' => $client_id,
    //         'client_secret' => $client_secret,
    //         'refresh_token' => $refresh_token,
    //         'grant_type' => 'refresh_token',
    //     ];

    //     // Initialize cURL
    //     $ch = curl_init();

    //     // Set the cURL options
    //     curl_setopt($ch, CURLOPT_URL, $token_url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Content-Type: application/x-www-form-urlencoded'
    //     ]);

    //     // Execute the cURL request and get the response
    //     $response = curl_exec($ch);

    //     // Check for cURL errors
    //     if (curl_errno($ch)) {
    //         die('Curl error: ' . curl_error($ch));
    //     }

    //     // Close the cURL session
    //     curl_close($ch);

    //     // Decode the JSON response
    //     $response_data = json_decode($response, true);
    //     print_r($response_data); exit;
    //     // Check if the response contains an error
    //     if (isset($response_data['error'])) {
    //         die('Error refreshing the token: ' . $response_data['error']);
    //     }

    //     // print_r($response_data); exit;
    //     // Extract the new access token
    //     $newAccessToken = $response_data['access_token'];
    //     $headers = [
    //         'Authorization: Bearer '.$newAccessToken,
    //         'Content-Type: application/json'
    //     ];

    //     $fields = '{
    //         "message": {
    //              "token":"'.$data['device_token'].'",
    //              "notification":{
    //                  "title":"'.$data['title'].'",
    //                  "body":"'.$data['body'].'"
    //              },
    //              "data": {
    //                 "request_id": "'.$data['request_id'].'"
    //             }
    //          }
    //      }';
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
    //     // print_r($ch); exit;
    //     $result = curl_exec($ch);
    //     if ($result === FALSE) {
    //         die('Curl failed: ' . curl_error($ch));
    //     }
    //     curl_close($ch);

    //     return json_decode($result, true);
    // }
}

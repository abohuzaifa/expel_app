<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
function removeImages($imageArray, $multi_images = 0) {
    // print_r($imageArray); exit;
    if($multi_images == 1)
    {
        foreach($imageArray as $img)
        {
            if(File::exists(public_path('/images/'.$img))) {
            //     echo "success"; exit;
                File::delete(public_path('/images/'.$img));
            }
        }
    } else {
        if(File::exists(public_path('/images/'.$imageArray))) {
            //     echo "success"; exit;
                File::delete(public_path('/images/'.$imageArray));
            }
    }
    
}

function formatDateTimeToEnglish($dateTimeString)
{
    $currentFormat = "Y-m-d H:i:s";
    // Parse the input date and time string using Carbon
    $dateTime = Carbon::createFromFormat($currentFormat, $dateTimeString);

    // Format the date and time to English with AM/PM
    $formattedDateTime = $dateTime->format('l, F j, Y g:i A');

    return $formattedDateTime;
}

function formatCreatedAt($created_at) {
    // Convert the created_at string to a DateTime object
    $createdDateTime = new DateTime($created_at);
    
    // Get the current date and time
    $currentDateTime = new DateTime();

    // Calculate the difference between the current date and the created_at date
    $interval = $currentDateTime->diff($createdDateTime);

    // Check the difference and format accordingly
    if ($interval->d > 0) {
        // Less than one hour, show in minutes
        return $interval->d . trans('lang.days_ago');
    } elseif ($interval->h < 24) {
        // Less than 24 hours, show in hours
        return $interval->h . trans('lang.hours_ago');
    } else {
        // More than 24 hours, show in days
        return $interval->i . trans('lang.minutes_ago');
    }
}
function generateRandomCode() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < 10; $i++) {
        $code .= $characters[mt_rand(0, $max)];
    }
    
    return $code;
}
function send_message($data, $mobile)
{
    $ch = curl_init();

    $payload = json_encode([
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $mobile,
        "type" => "template",
        "template" => [
            "name" => "parcel_receiver_address_template ",
            "language" => ["code" => "en"],
            "components" => [
                [
                    "type" => "body",
                    "parameters" => [
                        [
                            "type" => "text",
                            "text" => $data['code']
                        ]
                    ]
                ]
            ]
        ]
    ]);
    curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v21.0/378517282013470/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $headers = array();
    $headers[] = 'Authorization: Bearer EAAOh9TZC2MtUBOZCzd0p4u5w9YoFZAqLJF7sZBMsdDdirZCvZChVUj5UZCNv4yqZAwyXHFIEb4QFbv7qLDGhPFcwZA0fJuNYlfe23qx7wPeZCxaWdcNya1aRZC7hdZCuEpf49gWKk28rgT2twDWmFOq8yg7I3A2v1emQZBWdoQhPTu78UUjqNSwkBTBZBmhnUjSHF3yyjHDAZDZD';
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $result = json_decode($result, true);
    return $result;
}
function receiverWhatsappAddress($data)
{
    $ch = curl_init();

    $payload = json_encode([
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $data['mobile'],
        "type" => "template",
        "template" => [
            "name" => "parcel_receiver_address_template ",
            "language" => ["code" => "en"],
            "components" => [
                [
                    "type" => "button",
                    "index" => 0,
                    "sub_type" => "url",
                    "parameters" => [
                        [
                            "type" => "text",
                            "text" => "?data=".base64_encode($data['code'])
                        ]
                    ]
                ]
            ]
        ]
    ]);
    curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v21.0/378517282013470/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $headers = array();
    $headers[] = 'Authorization: Bearer EAAOh9TZC2MtUBOZCzd0p4u5w9YoFZAqLJF7sZBMsdDdirZCvZChVUj5UZCNv4yqZAwyXHFIEb4QFbv7qLDGhPFcwZA0fJuNYlfe23qx7wPeZCxaWdcNya1aRZC7hdZCuEpf49gWKk28rgT2twDWmFOq8yg7I3A2v1emQZBWdoQhPTu78UUjqNSwkBTBZBmhnUjSHF3yyjHDAZDZD';
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $result = json_decode($result, true);
    return $result;
}
function cleanText($text) {
    // Remove newlines and tabs
    $text = str_replace(array("\n", "\r", "\t"), ' ', $text);

    // Replace multiple spaces with a single space
    $text = preg_replace('/ {2,}/', ' ', $text);

    // Replace more than four consecutive spaces with four spaces
    $text = preg_replace('/ {5,}/', '    ', $text);

    return $text;
}

function subtractFivePercent($amount) {
    // Calculate 5% of the amount
    $percentage = $amount * 0.05;

    // Subtract 5% from the original amount
    $remainingAmount = $amount - $percentage;

    return $remainingAmount;
}

function getFivePercent($amount) {
    // Calculate 5% of the amount
    $percentage = $amount * 0.05;


    return $percentage;
}


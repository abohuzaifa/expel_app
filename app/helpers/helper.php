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



<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Location extends Controller
{
    //
    public function location(Request $req)
    {
        $data = $req->validate([
            "latitude" => "required",
            "longitude" => 'required',
            "category_id" => "required|int"
        ]);

        $radius = 10;
        
        $shops = DB::select("SELECT * FROM shops WHERE status=1 AND FIND_IN_SET(:cat_id, category_id) > 0", [":cat_id" => $data['category_id']]);
        if(count($shops) > 0)
        { //print_r($shops); exit;
            $near_shops = [];
            foreach ($shops as $key => $location) {
                $location->image_base_url = asset("images/");
                $storedLat = $location ->latitude; // Replace with your actual latitude key
                $storedLon = $location->longitude; // Replace with your actual longitude key
            
                $distance = $this->haversine($data['latitude'], $data['longitude'], $storedLat, $storedLon);
            
                if ($distance <= $radius) {
                    $near_shops[] = json_decode(json_encode($location), true);
                }
            }
            if(count($near_shops) > 0)
            {
                return response([
                    "status" => 1,
                    "shops" => $near_shops
                ]);
            } else {
                return response([
                    "status" => 0,
                    "shops" => []
                ]);
            }
        } else {
            return response([
                "status" => 0,
                "shops" => []
            ]);
        }
    }
    function haversine($lat1, $lon1, $lat2, $lon2) {
        $R = 6371; // Radius of the Earth in kilometers
    
        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);
    
        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dlon / 2) * sin($dlon / 2);
    
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $R * $c; // Distance in kilometers
    
        return $distance;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Request extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "user_id",
        "from_date",
        "to_date",
        "images",
        "parcel_lat",
        "parcel_long",
        "parcel_address",
        "receiver_lat",
        "receiver_long",
        "receiver_address",
        "receiver_mobile",
        "status",
        "offer_id",
        "channel_name",
        "category_id",
        'code',
        "payment_method"
    ];
    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function offers()
    {
        return $this->hasMany(Offer::class, 'request_id');
    }
    public function histories() {
        return $this->hasMany(History::class, 'request_id');
    }
    // Define the relationship with the Offer model
    public function offer() {
        return $this->belongsTo(Offer::class);
    }
}

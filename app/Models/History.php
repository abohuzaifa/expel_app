<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class History extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "request_id",
        "user_id",
        "lat",
        "long",
        "address",
        "is_read",
        "is_start",
        "is_end"
    ];

    public function request() {
        return $this->belongsTo(Request::class, 'request_id');
    }

}

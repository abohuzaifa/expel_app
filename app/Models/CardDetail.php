<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CardDetail extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        "user_id",
        "card_number",
        "cvv",
        "month",
        "year"
    ];
    protected $hidden = [
    ];
}

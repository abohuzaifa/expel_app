<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Cart extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        "product_id",
        "quantity",
        "user_id",
        "order_id",
        "is_manual"
    ];
    protected $hidden = [
    ];
}

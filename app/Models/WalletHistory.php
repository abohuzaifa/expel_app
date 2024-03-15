<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class WalletHistory extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'wallet_id',
        'amount',
        'is_deposite',
        'is_expanse',
        'description',
        'order_id',
        'invoice_id',
        'is_read'
    ];
}

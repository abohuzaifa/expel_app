<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class PaymentMethod extends Model
{
    use HasFactory;

    use HasFactory, HasApiTokens;

    protected $fillable = [
        "name",
        "name_ar",
        "public_key",
        "secret_key",
        "status",
        "created_by",
        "slug"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class City extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'cities';
}

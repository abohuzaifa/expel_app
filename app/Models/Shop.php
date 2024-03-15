<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Shop extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "name",
        "name_ar",
        "logo",
        "category_id",
        "location",
        "reg_no",
        "created_by",
        "description",
        "latitude",
        "longitude"
    ];
    protected $hidden = [
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

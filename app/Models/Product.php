<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        "p_name",
        "p_name_ar",
        "price",
        "images",
        "shop_id",
        "created_by",
        "status",
        "tax",
        "discount",
        "taxable",
        "tax_inclusive",
        "description",
    ];
    protected $hidden = [
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

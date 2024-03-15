<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class OrderItem extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "order_id",
        "product_id",
        "price",
        "item_quantity",
        "item_tax",
        "item_discount",
        "item_total",
        "item_description",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

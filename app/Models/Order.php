<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Order extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "user_id",
        "total",
        "paid",
        "due",
        "tax",
        "discount",
        "order_status",
        "is_approve",
        "description",
        "payment_method",
        "user_email",
        "user_address",
        "user_mobile",
        "manual_order",
        "pickup_date_time",
        "inv_date",
        "seller_id",
        "seller_description"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    public static function clickPayOrderStatus($data)
    {
        $invoice_id = $data['invoice_id'];
        $secret_key = $data['secret_key'];
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://secure.clickpay.com.sa/payment/invoice/$invoice_id/status",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: '.$secret_key
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    
    }
    public static function clickPay($data)
    {

        $invoice_items = $data['invoice_items'];

        $invoic_data = '{
            "profile_id": '.$data['profile_key'].',
            "tran_type": "sale",
            "tran_class": "ecom",
            "cart_currency": "SAR",
            "cart_amount": "'.$data['total'].'",
            "cart_id": "'.$data['order_id'].'",
            "cart_description": "'.$data['description'].'",
            "paypage_lang": "'.$data['language'].'",
            "customer_details": {
                '.$data['customer'].'
            },
            "hide_shipping": true,
            "callback": "'.$data['redirect_url'].'",
            "return": "'.$data['redirect_url'].'",
            "invoice": {
                "shipping_charges": '.$data['shipping_fee'].',
                "extra_charges": '.$data['extra_charges'].',
                "extra_discount": '.$data['extra_discount'].',
                "total": 0,
                "line_items": [
                    '.$invoice_items.'
                ]
            }
            }';
            // print_r($invoic_data); exit;
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://secure.clickpay.com.sa/payment/new/invoice',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $invoic_data,
        CURLOPT_HTTPHEADER => array(
            'authorization: '.$data['secret_key'].'',
            'Content-Type: application/json',
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }


    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method');
    }
    public function items()
    {
        return $this->belongsTo(OrderItem::class, 'payment_method');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}

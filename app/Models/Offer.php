<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Offer extends Model
{
    use HasFactory,HasApiTokens;

    protected $fillable = [
        'request_id',
        'amount',
        'user_id',
        'is_reject'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function clickPay($data)
    {

        $invoice_items = '{
            "sku": "'.$data['request_id'].'",
            "description": "'.$data['description'].'",
            "url": "",
            "unit_cost": '.round($data["amount"], 2).',
            "quantity": 1,
            "net_total": '.round($data['amount'], 2).',
            "discount_rate": 0,
            "discount_amount": 0,
            "tax_rate": 0,
            "tax_total": 0,
            "total": '.round($data['amount'], 2).'
        }';
        $user = auth()->user();

        $data['customer'] = '
        "name": "'.$user->name.'",
        "email": "'.$user->email.'",
        "phone": "'.$user->mobile.'",
        "street1": "'.$user->address.'"';

        $invoic_data = '{
            "profile_id": '.$data['profile_key'].',
            "tran_type": "sale",
            "tran_class": "ecom",
            "cart_currency": "SAR",
            "cart_amount": "'.$data['amount'].'",
            "cart_id": "'.$data['request_id'].'",
            "cart_description": "'.$data['description'].'",
            "paypage_lang": "ar",
            "customer_details": {
                '.$data['customer'].'
            },
            "hide_shipping": true,
            "callback": "'.url()->to('/success/'.base64_encode($data['request_id']).'/'.base64_encode($data['offer_id'])).'",
            "return": "'.url()->to('/success/'.base64_encode($data['request_id']).'/'.base64_encode($data['offer_id'])).'",
            "invoice": {
                "shipping_charges": 0,
                "extra_charges": 0,
                "extra_discount": 0,
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
    // Define the inverse relationship with the Request model
    public function requests() {
        return $this->hasMany(Request::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Wallet extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'user_id',
        'amount',
    ];

    public static function clickPay($data)
    {

        $invoice_items = $data['invoice_items'];

        $invoic_data = '{
            "profile_id": '.$data['profile_key'].',
            "tran_type": "sale",
            "tran_class": "ecom",
            "cart_currency": "SAR",
            "cart_amount": "'.$data['total'].'",
            "cart_id": "'.$data['wallet_id'].'",
            "cart_description": "'.$data['description'].'",
            "paypage_lang": "ar",
            "customer_details": {
                '.$data['customer'].'
            },
            "hide_shipping": true,
            "callback": "'.$data['redirect_url'].'",
            "return": "'.$data['redirect_url'].'",
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
}

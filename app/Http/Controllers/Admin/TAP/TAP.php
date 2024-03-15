<?php

namespace App\Http\Controllers\Admin\TAP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TAP extends Controller
{
    public $api_url;
    public $public_key;
    public $private_key;
    public $language;

    // create constructor
    public function __construct(){
        $this->api_url = "https://api.tap.company/v2/";
    }

    // invoice json data
    private $invoice_data = '
        {
            "draft": false,
            "due": inv_expiry,
            "expiry": inv_expiry,
            "description": "inv_description",
            "mode": "INVOICE",
            "note": "inv_note",
            "currencies": [
                "inv_currency"
            ],
            "charge": {
                "receipt": {
                    "email": false,
                    "sms": false
                },
                "statement_descriptor": ""
            },
            "customer": {
                "email": "customer_email",
                "first_name": "customer_first_name",
                "last_name": "customer_last_name",
                "middle_name": "",
                "phone": {
                    "country_code": "",
                    "number": "customer_mobile"
                }
            },
            "order": {
                "amount": inv_amount,
                "currency": "inv_currency",
                "items": [
                    invoice_items
                ]
                invoice_shipping
                tax_shipping

            },
            "payment_methods": [
                ""
            ],
            "post": {
                "url": ""
            },
            "redirect": {
                "url": "redirect_url"
            },
            "reference": {
                "invoice": "inv_reference",
                "order": "order_reference"
            }
        }

    ';
    // create invoice
    public function create_invoice($data){
        
        $url = $this->api_url . "invoices";
        $payload = $this->invoice_data;

        $payload = str_replace("inv_expiry", $data['inv_expiry'], $payload);
        $payload = str_replace("inv_description", $data['inv_currency'], $payload);
        $payload = str_replace("inv_note", $data['inv_note'], $payload);
        $payload = str_replace("inv_currency", $data['inv_currency'], $payload);
        $payload = str_replace("customer_email", $data['customer_email'], $payload);
        $payload = str_replace("customer_first_name", $data['customer_first_name'], $payload);
        $payload = str_replace("customer_last_name", $data['customer_last_name'], $payload);
        $payload = str_replace("customer_mobile", $data['customer_mobile'], $payload);
        // $payload = str_replace("inv_amount", $data['inv_amount'], $payload);
        
        $payload = str_replace("inv_tax_name", $data['inv_tax_name'], $payload);
        $payload = str_replace("inv_tax_type", $data['inv_tax_type'], $payload);
        $payload = str_replace("inv_tax_value", $data['inv_tax_value'], $payload);
    
        $payload = str_replace("redirect_url", $data['redirect_url'], $payload);
        $payload = str_replace("inv_reference", $data['inv_reference'], $payload);
        $payload = str_replace("order_reference", $data['order_reference'], $payload);
        $payload = str_replace("invoice_items", $data['inv_items'], $payload);
        
        // $shiptax = 0;
        if ($data['inv_shipping_amount'] > 0) {
            // $shiptax += $data["inv_shipping_amount"];
            $invoiceShipping = ',"shipping": {
                "amount": '.$data["inv_shipping_amount"].',
                "currency": "'.$data['inv_currency'].'",
                "provider": "'.$data['inv_shipping_provider'].'"
            }';    
        } else{
            $invoiceShipping = '';
        }
        
        $payload = str_replace("invoice_shipping", $invoiceShipping, $payload);

        $payload = str_replace("inv_amount", $data['inv_amount'], $payload);
        if($data['inv_tax_value'] > 0){
            $tax  = ',"tax":[{
                "description":"inv_tax_description",
                "name": "'.$data['inv_tax_name'].'",
                "rate":{
                    "type":"'.$data['inv_tax_type'].'",
                    "value":'.$data['inv_tax_value'].'
                } 
            }]';
        }else{
             $tax = '';
        }
        $payload = str_replace("tax_shipping", $tax, $payload);
        // echo '<pre>';
        // print_r($payload);
        // die;


        $headers = array(
            "language" => $data['language'],
            "private_key" => $data['private_key'],
        );
        // print_r($payload);

        $response = $this->curlRequest("invoices", $payload, $headers);
        // print_r($response);
        return $response;
    }


     // create function for curl request to TAP API URL with the data and return the response
     private function curlRequest($action, $payload, $headers, $method = "POST"){
        
        // create curl request
        $ch = curl_init();
        $url = $this->api_url . $action;
        $token = $headers['private_key'];
        $language = $headers['language'];

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'lang_code: '.$language,
                'Authorization: Bearer '.$token,
                'Content-Type: application/json'
            )
        );

        // print_r($options); exit;

        // set curl options
        curl_setopt_array($ch, $options);

        // echo curl_error($ch);
        // print_r( curl($ch) );
        // echo "here";
        // exit();

        // print_r($payload);
        
        // execute curl request
        $response = curl_exec($ch);

        // print_r($payload);
        // print_r($response);
        // exit();

        // covert raw to json
        $response = json_decode($response);

        // get the response code
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // print_r($response); //exit();
        
        // close curl request
        curl_close($ch);

        // check if the response code is 200
        if($response_code == 200){
            // return the response
            return json_encode(array("status" => "success", "message" => "Invoice Generated Successfully", "invoice_id" => $response->id, "url" => $response->url, "data" => $response));
        }

        // return the response
        return json_encode(array("status" => "error", "message" => "Error in the payment request", "data" => $response));
        
    }


    // get invoice
    public function checkPayment($data){
        // set the url
        $headers = array(
            "private_key" => $data['private_key'],
            "language" => "en"
            
        );
        $response = $this->curlRequest("invoices/".$data['invoice_id'], "", $headers, "GET");

        $response = json_decode($response);
        // print_r($response->data);
        // check if the status exists in the response
        if(isset($response->data->status) && !empty($response->data->status) && $response->data->status == "PAID"){
            
            // print_r($response);
            // return the response
            return json_encode(array("status" => "success", "message" => "Invoice Retrieved Successfully", "data" => $response->data));
            
        }else{
            // return the response
            return json_encode(array("status" => "error", "message" => "Error in the request", "data" => $response));
        }
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClickPayController extends Controller
{
    /**
     * Create a ClickPay invoice.
     *
     * Expects JSON body:
     * - request_id (required)
     * - offer_id (optional, for callback)
     * - description (required)
     * - amount (required)
     * - language (optional, defaults to "ar")
     *
     * Returns the full API response plus the JSON payload that was sent.
     */
   public function createInvoice(Request $request)
{
    $request->validate([
        'request_id'  => 'required',
        'offer_id'    => 'nullable',
        'description' => 'required|string',
        'amount'      => 'required|numeric|min:0.01',
        'language'    => 'nullable|string|in:ar,en',
    ]);

    $profileId  = config('services.clickpay.profile_id');
    $secretKey  = config('services.clickpay.secret_key');

    if (empty($profileId) || empty($secretKey)) {
        return response()->json([
            'success' => false,
            'message' => 'ClickPay credentials are not configured.',
        ], 500);
    }

    $requestId  = $request->input('request_id');
    $offerId    = $request->input('offer_id');
    $description = $request->input('description');
    $amount     = round($request->input('amount'), 2);
    $language   = $request->input('language', 'ar');

    // Build invoice items
    $invoiceItems = [
        [
            'sku'             => (string) $requestId,
            'description'     => $description,
            'url'             => '',
            'unit_cost'       => $amount,
            'quantity'        => 1,
            'net_total'       => $amount,
            'discount_rate'   => 0,
            'discount_amount' => 0,
            'tax_rate'        => 0,
            'tax_total'       => 0,
            'total'           => $amount,
        ],
    ];

    $encodedRequestId = base64_encode((string) $requestId);
    $encodedOfferId   = $offerId ? base64_encode((string) $offerId) : '';

    // 1. BACKEND CALLBACK: Standard HTTPS link for ClickPay server to send payment results
    // Ensure your app URL uses 'https://' in your production environment
    $serverCallbackUrl = 'https://thardi.com/success/'.base64_encode($encodedRequestId);

    // 2. FRONTEND RETURN: Pure custom deep link to reopen your mobile application
    $mobileReturnUrl = 'https://thardi.com/success/'.base64_encode($encodedRequestId);

    // Build the full JSON payload
    $payload = [
        'profile_id'       => (int) $profileId,
        'tran_type'        => 'sale',
        'tran_class'       => 'ecom',
        'cart_currency'    => 'SAR',
        'cart_amount'      => (string) $amount,
        'cart_id'          => (string) $requestId,
        'cart_description' => $description,
        'paypage_lang'     => $language,
        'customer_details' => [
            'name'   => 'Customer Name', // ClickPay requires valid string placeholders if empty
            'email'  => 'customer@example.com',
            'phone'  => '0000000000',
            'street1' => 'Street Address',
        ],
        'hide_shipping'    => true,
        'callback'         => $serverCallbackUrl, // Set to HTTPS URL
        'return'           => $mobileReturnUrl,   // Set to mobile deep link
        'invoice'          => [
            'shipping_charges' => 0,
            'extra_charges'    => 0,
            'extra_discount'   => 0,
            'total'            => 0,
            'line_items'       => $invoiceItems,
        ],
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL            => 'https://secure.clickpay.com.sa/payment/new/invoice',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Authorization: ' . $secretKey,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);

    curl_close($curl);

    if ($curlError) {
        return response()->json([
            'success' => false,
            'message' => 'cURL error: ' . $curlError,
            'payload' => $payload,
        ], 500);
    }

    $decodedResponse = json_decode($response, true);

    return response()->json([
        'success'  => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'message'  => 'Invoice created successfully',
        'data'     => $decodedResponse ?: $response,
        'payload'  => $payload,
    ]);
}


    /**
     * Check the status of a ClickPay invoice.
     *
     * Expects JSON body:
     * - invoice_id (required) — the ClickPay invoice ID returned from createInvoice
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|string',
        ]);

        $invoiceId = $request->input('invoice_id');
        $secretKey = config('services.clickpay.secret_key');

        if (empty($secretKey)) {
            return response()->json([
                'success' => false,
                'message' => 'ClickPay secret key is not configured.',
            ], 500);
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => "https://secure.clickpay.com.sa/payment/invoice/{$invoiceId}/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . $secretKey,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            return response()->json([
                'success' => false,
                'message' => 'cURL error: ' . $curlError,
            ], 500);
        }

        $decodedResponse = json_decode($response, true);

        return response()->json([
            'success'   => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data'      => $decodedResponse ?: $response,
        ]);
    }
}

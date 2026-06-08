<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.api_url');
        $this->apiKey = config('services.sms.api_key');
    }

    public function send($to, $message)
    {
        if(config('app.OTP_ACTIVATION') == '0'){
            Log::info('SMS sending skipped - OTP_ACTIVATION is disabled', [
                'to' => $to,
                'reason' => 'otp_activation_disabled'
            ]);
            return null;
        }

        // Log the attempt
        Log::info('SMS sending attempt', [
            'to' => $to,
            'api_url' => $this->apiUrl,
            'sender_id' => config('services.sms.sender_id'),
            'message_length' => strlen($message)
        ]);

        $data = [
            "api_key" => $this->apiKey,
            "senderid" => config('services.sms.sender_id'),
            "number" => $to,
            "message" => $message,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log the raw response
        Log::info('SMS API response received', [
            'to' => $to,
            'http_code' => $httpCode,
            'raw_response' => $response,
            'curl_error' => $curlError ?: null
        ]);

        if ($curlError) {
            Log::error('SMS cURL error', [
                'to' => $to,
                'error' => $curlError,
                'api_url' => $this->apiUrl
            ]);
            return null;
        }

        $decodedResponse = json_decode($response, true);

        // Log the decoded response
        if ($decodedResponse) {
            Log::info('SMS decoded response', [
                'to' => $to,
                'response' => $decodedResponse,
                'success' => isset($decodedResponse['response_code']) && $decodedResponse['response_code'] == 202
            ]);
        } else {
            Log::error('SMS response JSON decode failed', [
                'to' => $to,
                'raw_response' => $response
            ]);
        }

        return $decodedResponse;
    }
}

<?php

namespace App\Services\Delivery\Fraudcheck;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FraudCheckService
{
    private string $baseUrl = 'https://fraudbd.com';
    private string $apiKey;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->apiKey = config('services.fraudbd.api_key', env('FRAUD_BD_API_KEY', ''));
        $this->username = config('services.fraudbd.username', env('FRAUD_BD_USERNAME', ''));
        $this->password = config('services.fraudbd.password', env('FRAUD_BD_PASSWORD', ''));
    }

    /**
     * Check courier information for a phone number
     *
     * @param string $phoneNumber
     * @return array Always returns ['error' => null|'message', 'result' => [vendor => data]|null]
     */
    public function checkCourierInfo(string $phoneNumber): array
    {
        try {
            // Validate phone number format
            if (!$this->isValidBangladeshiPhone($phoneNumber)) {
                return [
                    'error' => 'Invalid phone number. Must be a valid Bangladeshi number starting with 01 and followed by 9 digits. For example: 01XXXXXXXXX.',
                    'result' => null
                ];
            }

            // Normalize phone number for cache key
            $normalizedPhone = preg_replace('/\D/', '', $phoneNumber);
            $cacheKey = 'fraud_check_' . $normalizedPhone;

            // Check if data exists in cache
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                Log::info('Fraud BD Cache Hit', ['phone_number' => $normalizedPhone]);
                return [
                    'error' => null,
                    'result' => $cachedData
                ];
            }

            Log::info('Fraud BD Cache Miss', ['phone_number' => $normalizedPhone]);

            // Make API request with increased timeout
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
                'api_key' => $this->apiKey,
                'user_name' => $this->username,
                'password' => $this->password,
            ])->post($this->baseUrl . '/api/check-courier-info', [
                'phone_number' => $phoneNumber
            ]);

            // Check if request was successful
            if (!$response->successful()) {
                $errorMessage = 'Failed to fetch courier information. Please try again later.';

                // Handle specific timeout errors
                if ($response->clientError() && str_contains($response->body(), 'timeout')) {
                    $errorMessage = 'Request timed out. The service is taking too long to respond. Please try again later.';
                }

                Log::error('Fraud BD API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'phone_number' => $phoneNumber
                ]);

                return [
                    'error' => $errorMessage,
                    'result' => null
                ];
            }

            $data = $response->json();

            // Check API response status
            if (!isset($data['status']) || $data['status'] !== true) {
                $errorMessage = $data['message'] ?? 'Unknown error occurred';
                Log::error('Fraud BD API Response Error', [
                    'phone_number' => $phoneNumber,
                    'response' => $data
                ]);

                return [
                    'error' => $errorMessage,
                    'result' => null
                ];
            }

            // Extract and format the data
            if (!isset($data['data']['Summaries'])) {
                return [
                    'error' => 'No courier information found for this number.',
                    'result' => null
                ];
            }

            $summaries = $data['data']['Summaries'];
            $formattedResult = [];

            foreach ($summaries as $courierName => $courierData) {
                $formattedResult[$courierName] = $this->formatCourierData($courierData);
            }

            // Store in cache for 3 months
            $threeMonthsInSeconds = 60 * 60 * 24 * 90; // 90 days = 3 months
            Cache::put($cacheKey, $formattedResult, $threeMonthsInSeconds);
            Log::info('Fraud BD Data Cached', ['phone_number' => $normalizedPhone, 'ttl' => '90 days']);

            return [
                'error' => null,
                'result' => $formattedResult
            ];
        } catch (\Exception $e) {
            $errorMessage = 'An error occurred while checking courier information. Please try again later.';

            // Handle specific timeout exceptions
            if (str_contains($e->getMessage(), 'timeout') || str_contains($e->getMessage(), 'Connection timed out')) {
                $errorMessage = 'Connection timed out. The external service is not responding. Please try again later.';
            }

            Log::error('Fraud BD Service Exception', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => $errorMessage,
                'result' => null
            ];
        }
    }

    /**
     * Format courier data for consistent response
     *
     * @param array $courierData
     * @return array
     */
    private function formatCourierData(array $courierData): array
    {
        $formatted = [
            'logo' => $courierData['logo'] ?? null,
            'data_type' => $courierData['data_type'] ?? 'unknown',
        ];

        // Handle rating-based data (Pathao)
        if ($courierData['data_type'] === 'rating') {
            $formatted['customer_rating'] = $courierData['customer_rating'] ?? null;
            $formatted['risk_level'] = $courierData['risk_level'] ?? null;
            $formatted['message'] = $courierData['message'] ?? null;
            $formatted['success_rate'] = $courierData['success_rate'] ?? null;
        }

        // Handle delivery-based data (Steadfast, Paperfly, RedX)
        if ($courierData['data_type'] === 'delivery') {
            $formatted['total'] = $courierData['total'] ?? 0;
            $formatted['success'] = $courierData['success'] ?? 0;
            $formatted['cancel'] = $courierData['cancel'] ?? 0;

            if ($formatted['total'] > 0) {
                $formatted['success_rate'] = round(($formatted['success'] / $formatted['total']) * 100, 2);
                $formatted['cancel_rate'] = round(($formatted['cancel'] / $formatted['total']) * 100, 2);
            }
        }

        return $formatted;
    }

    /**
     * Validate Bangladeshi phone number
     *
     * @param string $phone
     * @return bool
     */
    private function isValidBangladeshiPhone(string $phone): bool
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Check if it starts with 8801 or 01 and has 11-13 digits total
        if (preg_match('/^(8801|01)[0-9]{9}$/', $phone)) {
            return true;
        }

        return false;
    }
}

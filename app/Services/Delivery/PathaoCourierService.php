<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use App\Services\Interfaces\DeliveryPartnerInterface;

class PathaoCourierService implements DeliveryPartnerInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $username;
    protected $password;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.pathao.client_id');
        $this->clientSecret = config('services.pathao.client_secret');
        $this->username = config('services.pathao.username');
        $this->password = config('services.pathao.password');
        $this->baseUrl = config('services.pathao.base_url', 'https://courier-api-sandbox.pathao.com');
    }

    protected function getToken(): string
    {
        return Cache::remember('pathao_access_token', 431000, function () {
            $response = Http::post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'password',
                'username'      => $this->username,
                'password'      => $this->password,
            ]);

            if ($response->failed()) {
                Log::error('Pathao token retrieval failed', [
                    'endpoint' => "{$this->baseUrl}/aladdin/api/v1/issue-token",
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'client_id' => $this->clientId,
                ]);
                throw new \Exception('Failed to retrieve access token from Pathao: ' . $response->body());
            }

            $data = $response->json();
            Cache::put('pathao_refresh_token', $data['refresh_token'], 432000);
            return $data['access_token'];
        });
    }

    protected function refreshToken(): string
    {
        $refreshToken = Cache::get('pathao_refresh_token');

        if (!$refreshToken) {
            return $this->getToken();
        }

        $response = Http::post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->failed()) {
            Log::error('Pathao token refresh failed', [
                'endpoint' => "{$this->baseUrl}/aladdin/api/v1/issue-token",
                'status' => $response->status(),
                'response' => $response->body(),
                'has_refresh_token' => !empty($refreshToken),
            ]);
            throw new \Exception('Failed to refresh token: ' . $response->body());
        }

        $data = $response->json();
        Cache::put('pathao_access_token', $data['access_token'], 431000);
        Cache::put('pathao_refresh_token', $data['refresh_token'], 432000);
        return $data['access_token'];
    }

    /**
     * Reusable authenticated HTTP client.
     */
    protected function withAuth(): \Illuminate\Http\Client\PendingRequest
    {
        try {
            return Http::withToken($this->getToken());
        } catch (\Throwable $e) {
            return Http::withToken($this->refreshToken());
        }
    }

    /**
     * Create a Pathao order.
     */
    public function createOrder(object $data): array
    {
        $response = $this->withAuth()
            ->post("{$this->baseUrl}/aladdin/api/v1/orders", (array) $data);

        if ($response->status() === 401) {
            Log::warning('Pathao order creation received 401, retrying with fresh token', [
                'merchant_order_id' => $data->merchant_order_id ?? null,
            ]);
            $response = $this->withAuth()
                ->post("{$this->baseUrl}/aladdin/api/v1/orders", (array) $data);
        }

        if ($response->failed()) {
            Log::error('Pathao order creation failed', [
                'endpoint' => "{$this->baseUrl}/aladdin/api/v1/orders",
                'status' => $response->status(),
                'merchant_order_id' => $data->merchant_order_id ?? null,
                'recipient_phone' => $data->recipient_phone ?? null,
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to create order in Pathao: ' . $response->body());
        }

        Log::info('Pathao order created successfully', [
            'merchant_order_id' => $data->merchant_order_id ?? null,
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Get list of cities.
     */
    public function getCities(): array
    {
        $response = $this->withAuth()->get("{$this->baseUrl}/aladdin/api/v1/city-list");

        if ($response->failed()) {
            Log::error('Pathao cities fetch failed', [
                'endpoint' => "{$this->baseUrl}/aladdin/api/v1/city-list",
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception("Failed to fetch Pathao cities: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get zones within a given city.
     */
    public function getZones(int $cityId): array
    {
        $response = $this->withAuth()->get("{$this->baseUrl}/aladdin/api/v1/cities/{$cityId}/zone-list");

        if ($response->failed()) {
            Log::error('Pathao zones fetch failed', [
                'endpoint' => "{$this->baseUrl}/aladdin/api/v1/cities/{$cityId}/zone-list",
                'city_id' => $cityId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception("Failed to fetch Pathao zones: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get areas within a given zone.
     */
    public function getAreas(int $zoneId): array
    {
        $response = $this->withAuth()->get("{$this->baseUrl}/aladdin/api/v1/zones/{$zoneId}/area-list");

        if ($response->failed()) {
            Log::error('Pathao areas fetch failed', [
                'endpoint' => "{$this->baseUrl}/aladdin/api/v1/zones/{$zoneId}/area-list",
                'zone_id' => $zoneId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception("Failed to fetch Pathao areas: " . $response->body());
        }

        return $response->json();
    }
    public function getDeliveryStatus(string $trackingId): array
    {
        $response = $this->withAuth()
            ->get("{$this->baseUrl}/aladdin/api/v1/orders/{$trackingId}/info");

        if ($response->failed()) {
            Log::error('Pathao delivery status fetch failed', [
                'endpoint' => "{$this->baseUrl}/aladdin/api/v1/orders/{$trackingId}/info",
                'tracking_id' => $trackingId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception("Failed to fetch delivery status: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get merchant/account balance from Pathao.
     * Pathao API variants differ by account, so try known endpoints.
     */
    public function getBalance(): array
    {
        $endpoints = [
            '/aladdin/api/v1/user-balance',
            '/aladdin/api/v1/merchant/payment-info',
            '/aladdin/api/v1/merchant/balance',
        ];

        $lastError = null;
        $failedEndpoints = [];

        foreach ($endpoints as $endpoint) {
            $response = $this->withAuth()->get("{$this->baseUrl}{$endpoint}");

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data)) {
                    $data['_endpoint'] = $endpoint;
                    return $data;
                }

                return [
                    '_endpoint' => $endpoint,
                    'raw' => $data,
                ];
            }

            $failedEndpoints[] = [
                'endpoint' => $endpoint,
                'status' => $response->status(),
            ];

            $lastError = sprintf(
                'Pathao balance endpoint %s failed with status %s',
                $endpoint,
                $response->status(),
            );
        }

        Log::error('Pathao balance fetch failed on all endpoints', [
            'failed_endpoints' => $failedEndpoints,
        ]);

        throw new \Exception($lastError ?? 'Failed to fetch balance from Pathao');
    }
}

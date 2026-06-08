<?php

namespace App\Services\Delivery;

use App\Services\Interfaces\DeliveryPartnerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedxService implements DeliveryPartnerInterface
{
    protected ?string $baseUrl = null;
    protected ?string $apiKey = null;

    public function __construct()
    {
        $this->baseUrl = config('services.redx.base_url');
        $this->apiKey = config('services.redx.api_key');
    }

    protected function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->apiKey);
    }

    protected function notConfiguredError(): array
    {
        return [
            'status' => 'error',
            'message' => 'Redx service is not configured. Please set REDX_BASE_URL and REDX_API_KEY in .env',
        ];
    }

    protected function headers(): array
    {
        return [
            'API-ACCESS-TOKEN' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(object $data): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception($this->notConfiguredError()['message']);
        }

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/parcel", (array) $data);

        if ($response->failed()) {
            Log::error('Redx order creation failed', [
                'endpoint' => "{$this->baseUrl}/parcel",
                'status' => $response->status(),
                'merchant_invoice_id' => $data->merchant_invoice_id ?? null,
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to create order in Redx: ' . $response->body());
        }

        Log::info('Redx order created successfully', [
            'merchant_invoice_id' => $data->merchant_invoice_id ?? null,
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    public function bulkCreateOrders(array $orders): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception($this->notConfiguredError()['message']);
        }

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/bulk-parcel", ['data' => $orders]);

        if ($response->failed()) {
            Log::error('Redx bulk order creation failed', [
                'endpoint' => "{$this->baseUrl}/bulk-parcel",
                'status' => $response->status(),
                'order_count' => count($orders),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to create bulk orders in Redx: ' . $response->body());
        }

        return $response->json();
    }

    public function getDeliveryStatus(string $trackingId): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception($this->notConfiguredError()['message']);
        }

        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/parcel/track/{$trackingId}");

        if ($response->failed()) {
            Log::error('Redx delivery status fetch failed', [
                'endpoint' => "{$this->baseUrl}/parcel/track/{$trackingId}",
                'tracking_id' => $trackingId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch delivery status from Redx: ' . $response->body());
        }

        return $response->json();
    }

    public function getBalance(): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception($this->notConfiguredError()['message']);
        }

        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/merchant/balance");

        if ($response->failed()) {
            Log::error('Redx balance fetch failed', [
                'endpoint' => "{$this->baseUrl}/merchant/balance",
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch balance from Redx: ' . $response->body());
        }

        return $response->json();
    }

    public function getAreas(): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception($this->notConfiguredError()['message']);
        }

        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/areas");

        if ($response->failed()) {
            Log::error('Redx areas fetch failed', [
                'endpoint' => "{$this->baseUrl}/areas",
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch areas from Redx: ' . $response->body());
        }

        return $response->json();
    }
}

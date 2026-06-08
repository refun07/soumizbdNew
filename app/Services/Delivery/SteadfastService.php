<?php

namespace App\Services\Delivery;

use App\Services\Interfaces\DeliveryPartnerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastService implements DeliveryPartnerInterface
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.steadfast.base_url');
        $this->apiKey = config('services.steadfast.api_key');
        $this->secretKey = config('services.steadfast.secret_key');
    }

    protected function headers()
    {
        return [
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(object $data): array
    {
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/create_order", (array) $data);

        if ($response->failed()) {
            Log::error('Steadfast order creation failed', [
                'endpoint' => "{$this->baseUrl}/create_order",
                'status' => $response->status(),
                'invoice' => $data->invoice ?? null,
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to create order in Steadfast: ' . $response->body());
        }

        Log::info('Steadfast order created successfully', [
            'invoice' => $data->invoice ?? null,
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    public function bulkCreateOrders(array $orders)
    {
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/bulk_order_create", ['orders' => $orders]);

        if ($response->failed()) {
            Log::error('Steadfast bulk order creation failed', [
                'endpoint' => "{$this->baseUrl}/bulk_order_create",
                'status' => $response->status(),
                'order_count' => count($orders),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to create bulk orders in Steadfast: ' . $response->body());
        }

        return $response->json();
    }

    public function getDeliveryStatus(string $trackingId)
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/status_by_trackingcode/{$trackingId}");

        if ($response->failed()) {
            Log::error('Steadfast delivery status fetch failed', [
                'endpoint' => "{$this->baseUrl}/status_by_trackingcode/{$trackingId}",
                'tracking_id' => $trackingId,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch delivery status from Steadfast: ' . $response->body());
        }

        return $response->json();
    }

    public function getBalance()
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/get_balance");

        if ($response->failed()) {
            Log::error('Steadfast balance fetch failed', [
                'endpoint' => "{$this->baseUrl}/get_balance",
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch balance from Steadfast: ' . $response->body());
        }

        return $response->json();
    }
}

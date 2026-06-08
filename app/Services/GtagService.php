<?php

// app/Services/GtagService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class GtagService
{
    protected string $measurementId;
    protected string $apiSecret;
    protected string|null $clientId;

    public function __construct()
    {
        $this->measurementId = config('services.ga4.measurement_id');
        $this->apiSecret = config('services.ga4.api_secret');
        $this->clientId = $this->extractClientId();
    }

    protected function extractClientId(): ?string
    {
        $ga = Request::cookie('_ga');

        if ($ga) {
            $parts = explode('.', $ga);
            if (count($parts) === 4) {
                return $parts[2] . '.' . $parts[3];
            }
        }

        return null;
    }

    public function sendEvent(string $eventName, array $params = []): void
    {
        if(config('app.GOOGLE_ANALYTICS_ACTIVATION') == '0' ){
            return ;
        }
        if (!$this->clientId) return;

        Http::post("https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}", [
            'client_id' => $this->clientId,
            'events' => [
                [
                    'name' => $eventName,
                    'params' => $params,
                ]
            ]
        ]);
    }
}

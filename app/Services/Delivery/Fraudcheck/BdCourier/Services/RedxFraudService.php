<?php

namespace App\Services\Delivery\Fraudcheck\BdCourier\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Services\Delivery\Fraudcheck\BdCourier\Cache\FileTokenCache;
use App\Services\Delivery\Fraudcheck\BdCourier\Config\FraudCheckerConfig;
use App\Services\Delivery\Fraudcheck\BdCourier\Helpers\CourierDataValidator;
use App\Services\Delivery\Fraudcheck\BdCourier\Contracts\CourierServiceInterface;

/**
 * Class RedxFraudService
 *
 * Handles API interactions with RedX courier to fetch delivery statistics.
 * Uses caching to store the access token and prevent hitting login rate limits.
 *
 * @package App\Services\Delivery\Fraudcheck\BdCourier\Services
 */
readonly class RedxFraudService implements CourierServiceInterface
{
    /**
     * @var string The cache key used to store the RedX access token.
     */
    protected string $cacheKey;

    /**
     * @var int The token expiration time in minutes.
     */
    protected int $cacheMinutes;

    /**
     * @var string The login phone number for RedX API.
     */
    protected string $phone;

    /**
     * @var string The password for RedX API authentication.
     */
    protected string $password;

    /**
     * @var FileTokenCache
     */
    protected FileTokenCache $cache;

    /**
     * @var Client
     */
    protected Client $httpClient;

    /**
     * RedxFraudService constructor.
     *
     * @param FraudCheckerConfig $config
     * @param FileTokenCache     $cache
     * @param Client|null        $httpClient
     */
    public function __construct(FraudCheckerConfig $config, FileTokenCache $cache, ?Client $httpClient = null)
    {
        $this->cacheKey = 'redx_access_token';
        $this->cacheMinutes = 50;
        $this->cache = $cache;
        $this->httpClient = $httpClient ?? new Client(['timeout' => 15.0]);

        // Validate config presence
        CourierDataValidator::enforceConfig($config, [
            'redx.phone',
            'redx.password',
        ]);

        // Load from config
        $this->phone = $config->get('redx.phone');
        $this->password = $config->get('redx.password');

        CourierDataValidator::checkBdMobile($this->phone);
    }

    /**
     * Retrieve a valid RedX access token from the cache or authenticate to get a new one.
     *
     * @return string|null The access token, or null on failure.
     */
    protected function getAccessToken(): ?string
    {
        // Use cached token if available
        $token = $this->cache->get($this->cacheKey);
        if ($token) {
            return $token;
        }

        // Request new token from RedX
        try {
            $response = $this->httpClient->post('https://api.redx.com.bd/v4/auth/login', [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'Accept' => 'application/json, text/plain, */*',
                ],
                'json' => [
                    'phone' => '88' . $this->phone,
                    'password' => $this->password,
                ],
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() >= 400) {
                Log::error('Redx login failed', [
                    'status' => $response->getStatusCode(),
                    'response' => $response->getBody()->getContents()
                ]);
                return null;
            }

            $data = json_decode($response->getBody()->getContents(), true);
            $token = $data['data']['accessToken'] ?? null;

            if ($token) {
                $this->cache->put($this->cacheKey, $token, $this->cacheMinutes);
            } else {
                Log::error('Redx access token not found in response', [
                    'response' => $data
                ]);
            }

            return $token;
        } catch (GuzzleException $e) {
            Log::error('Redx login exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Fetch delivery statistics from RedX for the given phone number.
     *
     * @param string $queryPhone The Bangladeshi mobile number to check.
     * @return array Contains 'success', 'cancel', 'total', and 'success_ratio'.
     *               Returns an array with an 'error' key if the API request fails.
     */
    public function getDeliveryStats(string $queryPhone): array
    {
        try {
            CourierDataValidator::checkBdMobile($queryPhone);

            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::error('Redx access token not available', [
                    'phone_number' => $queryPhone
                ]);
                return ['error' => 'Login failed or unable to get access token from Redx'];
            }

            $response = $this->httpClient->get("https://redx.com.bd/api/redx_se/admin/parcel/customer-success-return-rate", [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'Accept' => 'application/json, text/plain, */*',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'query' => [
                    'phoneNumber' => '88' . $queryPhone
                ],
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() < 400) {
                $object = json_decode($response->getBody()->getContents(), true);

                $success = (int)($object['data']['deliveredParcels'] ?? 0);
                $total = (int)($object['data']['totalParcels'] ?? 0);
                $cancel = max(0, $total - $success);
                $success_ratio = $total > 0 ? round(($success / $total) * 100, 2) : 0;

                return [
                    'success' => $success,
                    'cancel' => $cancel,
                    'total' => $total,
                    'success_ratio' => $success_ratio,
                ];
            } elseif ($response->getStatusCode() === 401) {
                Log::error('Redx access token expired or invalid', [
                    'phone_number' => $queryPhone,
                    'status' => 401
                ]);
                $this->cache->forget($this->cacheKey);
                return ['error' => 'Access token expired or invalid for Redx. Please retry.', 'status' => 401];
            }

            Log::error('Redx API threshold hit or other error', [
                'phone_number' => $queryPhone,
                'status' => $response->getStatusCode(),
                'response' => $response->getBody()->getContents()
            ]);

            return [
                'success' => 0,
                'cancel' => 0,
                'total' => 0,
                'success_ratio' => 0,
                'error' => 'Threshold hit, wait a minute for Redx',
                'status' => $response->getStatusCode(),
            ];
        } catch (GuzzleException | \Exception $e) {
            Log::error('Redx fraud check failed', [
                'phone_number' => $queryPhone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => 'An error occurred while processing Redx request',
                'message' => $e->getMessage()
            ];
        }
    }
}

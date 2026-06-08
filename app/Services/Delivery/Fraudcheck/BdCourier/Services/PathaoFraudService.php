<?php

namespace App\Services\Delivery\Fraudcheck\BdCourier\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use App\Services\Delivery\Fraudcheck\BdCourier\Config\FraudCheckerConfig;
use App\Services\Delivery\Fraudcheck\BdCourier\Helpers\CourierDataValidator;
use App\Services\Delivery\Fraudcheck\BdCourier\Contracts\CourierServiceInterface;

/**
 * Class PathaoFraudService
 *
 * Handles API interactions with Pathao courier to fetch delivery statistics
 * for a specific customer phone number.
 *
 * @package App\Services\Delivery\Fraudcheck\BdCourier\Services
 */
readonly class PathaoFraudService implements CourierServiceInterface
{
    /**
     * @var string The username for Pathao API authentication.
     */
    protected string $username;

    /**
     * @var string The password for Pathao API authentication.
     */
    protected string $password;

    /**
     * @var Client
     */
    protected Client $httpClient;

    /**
     * PathaoFraudService constructor.
     *
     * @param FraudCheckerConfig $config
     * @param Client|null        $httpClient
     */
    public function __construct(FraudCheckerConfig $config, ?Client $httpClient = null)
    {
        CourierDataValidator::enforceConfig($config, [
            'pathao.user',
            'pathao.password',
        ]);

        $this->username = $config->get('pathao.user');
        $this->password = $config->get('pathao.password');
        $this->httpClient = $httpClient ?? new Client(['timeout' => 15.0]);
    }

    /**
     * Fetch delivery statistics from Pathao for the given phone number.
     *
     * @param string $phoneNumber The Bangladeshi mobile number to check.
     * @return array Contains 'success', 'cancel', 'total', and 'success_ratio'.
     *               In case of an error, returns an array with an 'error' key.
     */
    public function getDeliveryStats(string $phoneNumber): array
    {
        try {
            CourierDataValidator::checkBdMobile($phoneNumber);

            $response = $this->httpClient->post('https://merchant.pathao.com/api/v1/login', [
                'json' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
                'http_errors' => false,
            ]);

            if ($response->getStatusCode() >= 400) {
                Log::error('Pathao authentication failed', [
                    'phone_number' => $phoneNumber,
                    'status' => $response->getStatusCode(),
                    'response' => $response->getBody()->getContents()
                ]);
                return ['error' => 'Failed to authenticate with Pathao', 'status' => $response->getStatusCode()];
            }

            $data = json_decode($response->getBody()->getContents(), true);
            $accessToken = trim($data['access_token'] ?? '');

            if (!$accessToken) {
                Log::error('Pathao access token not received', [
                    'phone_number' => $phoneNumber,
                    'response' => $data
                ]);
                return ['error' => 'No access token received from Pathao'];
            }

            $responseAuth = $this->httpClient->post('https://merchant.pathao.com/api/v1/user/success', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => [
                    'phone' => $phoneNumber,
                ],
                'http_errors' => false,
            ]);

            if ($responseAuth->getStatusCode() >= 400) {
                Log::error('Pathao customer data retrieval failed', [
                    'phone_number' => $phoneNumber,
                    'status' => $responseAuth->getStatusCode(),
                    'response' => $responseAuth->getBody()->getContents()
                ]);
                return ['error' => 'Failed to retrieve customer data from Pathao', 'status' => $responseAuth->getStatusCode()];
            }

            $object = json_decode($responseAuth->getBody()->getContents(), true);

            Log::info('Pathao fraud check response', [
                'phone_number' => $phoneNumber,
                'response' => $object
            ]);

            // Extract data from customer object if available, otherwise use top-level
            $customerData = $object['data']['customer'] ?? $object['data'] ?? [];
            $success = (int)($customerData['successful_delivery'] ?? 0);
            $total = (int)($customerData['total_delivery'] ?? 0);
            $customerRating = $object['data']['customer_rating'] ?? null;
            $cancel = max(0, $total - $success);
            $success_ratio = $total > 0 ? round(($success / $total) * 100, 2) : 0;

            return [
                'success' => $success,
                'cancel' => $cancel,
                'total' => $total,
                'success_ratio' => $success_ratio,
                'customer_rating' => $customerRating,
            ];
        } catch (GuzzleException | \Exception $e) {
            Log::error('Pathao fraud check failed', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => 'An error occurred while processing Pathao request',
                'message' => $e->getMessage()
            ];
        }
    }
}

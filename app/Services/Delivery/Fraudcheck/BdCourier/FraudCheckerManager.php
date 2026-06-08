<?php

namespace App\Services\Delivery\Fraudcheck\BdCourier;

use App\Services\Delivery\Fraudcheck\BdCourier\Cache\FileTokenCache;
use App\Services\Delivery\Fraudcheck\BdCourier\Config\FraudCheckerConfig;
use App\Services\Delivery\Fraudcheck\BdCourier\Services\SteadfastFraudService;
use App\Services\Delivery\Fraudcheck\BdCourier\Services\PathaoFraudService;
use App\Services\Delivery\Fraudcheck\BdCourier\Services\RedxFraudService;
use Illuminate\Support\Facades\Log;

/**
 * Class FraudCheckerManager
 *
 * Core manager class responsible for aggregating delivery statistics
 * from multiple courier services (Steadfast, Pathao, RedX).
 *
 * @package App\Services\Delivery\Fraudcheck\BdCourier
 */
class FraudCheckerManager
{
    /**
     * @var FraudCheckerConfig The configuration instance for the manager
     */
    protected FraudCheckerConfig $config;

    /**
     * @var SteadfastFraudService
     */
    protected SteadfastFraudService $steadfastService;

    /**
     * @var PathaoFraudService
     */
    protected PathaoFraudService $pathaoService;

    /**
     * @var RedxFraudService
     */
    protected RedxFraudService $redxService;

    /**
     * FraudCheckerManager constructor.
     *
     * Initializes the manager with hardcoded credentials.
     *
     * @param array $customConfig Optional custom configuration to override defaults
     */
    public function __construct(array $customConfig = [])
    {
        $defaultConfig = [
            'steadfast' => [
                'user' => config('app.FRAUDCHECK_STEADFAST_USER', ''),
                'password' => config('app.FRAUDCHECK_STEADFAST_PASSWORD', ''),
            ],
            'pathao' => [
                'user' => config('app.FRAUDCHECK_PATHAO_USER', ''),
                'password' => config('app.FRAUDCHECK_PATHAO_PASSWORD', ''),
            ],
            'redx' => [
                'phone' => config('app.FRAUDCHECK_REDX_PHONE', ''),
                'password' => config('app.FRAUDCHECK_REDX_PASSWORD', ''),
            ],
        ];

        $this->config = new FraudCheckerConfig(array_merge_recursive($defaultConfig, $customConfig));
        
        // Initialize services
        $this->initializeServices();
    }

    /**
     * Initialize all courier services
     *
     * @return void
     */
    protected function initializeServices(): void
    {
        $this->steadfastService = new SteadfastFraudService($this->config);
        $this->pathaoService = new PathaoFraudService($this->config);
        
        $cache = new FileTokenCache();
        $this->redxService = new RedxFraudService($this->config, $cache);
    }

    /**
     * Fetch delivery statistics across all configured couriers and aggregate the results.
     *
     * @param string $phoneNumber The Bangladeshi mobile number to check.
     * @return array Returns an associative array containing stats for each courier
     *               as well as an overall aggregated summary.
     */
    public function check(string $phoneNumber): array
    {
        Log::debug('FraudCheckerManager: starting check', [
            'phone_number' => $phoneNumber,
        ]);

        $payload = [
            'steadfast' => null,
            'pathao' => null,
            'redx' => null,
            'aggregate' => [
                'total_success' => 0,
                'total_cancel' => 0,
                'total_deliveries' => 0,
                'success_ratio' => 0,
                'cancel_ratio' => 0,
            ]
        ];

        $services = [
            'steadfast' => $this->steadfastService,
            'pathao' => $this->pathaoService,
            'redx' => $this->redxService,
        ];

        $totalSuccessCount = 0;
        $totalCancelCount = 0;

        foreach ($services as $key => $service) {
            try {
                $stats = $service->getDeliveryStats($phoneNumber);
                $payload[$key] = $stats;

                Log::debug('FraudCheckerManager: service result', [
                    'service' => $key,
                    'stats' => $stats,
                ]);

                if (isset($stats['success'], $stats['cancel']) && is_numeric($stats['success']) && is_numeric($stats['cancel'])) {
                    $totalSuccessCount += (int)$stats['success'];
                    $totalCancelCount += (int)$stats['cancel'];
                }
            } catch (\Exception $e) {
                $payload[$key] = [
                    'error' => 'Service unavailable or failed to process',
                    'message' => $e->getMessage()
                ];
            }
        }

        $overallTotal = $totalSuccessCount + $totalCancelCount;

        $payload['aggregate']['total_success'] = $totalSuccessCount;
        $payload['aggregate']['total_cancel'] = $totalCancelCount;
        $payload['aggregate']['total_deliveries'] = $overallTotal;

        if ($overallTotal > 0) {
            $payload['aggregate']['success_ratio'] = round(($totalSuccessCount / $overallTotal) * 100, 2);
            $payload['aggregate']['cancel_ratio'] = round(($totalCancelCount / $overallTotal) * 100, 2);
        }

        Log::debug('FraudCheckerManager: aggregate', [
            'total_success' => $totalSuccessCount,
            'total_cancel' => $totalCancelCount,
            'total_deliveries' => $overallTotal,
        ]);

        return $payload;
    }

    /**
     * Get statistics from a specific courier only
     *
     * @param string $courier The courier name: 'steadfast', 'pathao', or 'redx'
     * @param string $phoneNumber The Bangladeshi mobile number to check
     * @return array
     * @throws \InvalidArgumentException
     */
    public function checkCourier(string $courier, string $phoneNumber): array
    {
        $services = [
            'steadfast' => $this->steadfastService,
            'pathao' => $this->pathaoService,
            'redx' => $this->redxService,
        ];

        if (!isset($services[$courier])) {
            throw new \InvalidArgumentException("Invalid courier: {$courier}. Valid options: steadfast, pathao, redx");
        }

        try {
            return $services[$courier]->getDeliveryStats($phoneNumber);
        } catch (\Exception $e) {
            return [
                'error' => 'Service unavailable or failed to process',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Update configuration dynamically
     *
     * @param array $config New configuration values
     * @return self
     */
    public function setConfig(array $config): self
    {
        $this->config->load($config);
        $this->initializeServices();
        return $this;
    }

    /**
     * Get current configuration
     *
     * @return FraudCheckerConfig
     */
    public function getConfig(): FraudCheckerConfig
    {
        return $this->config;
    }
}

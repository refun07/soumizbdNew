<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use App\Services\Delivery\Fraudcheck\FraudCheckService;
use App\Services\Delivery\Fraudcheck\BdCourier\FraudCheckerManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FraudCheckController extends Controller
{
    private FraudCheckService $fraudCheckService;

    public function __construct(FraudCheckService $fraudCheckService)
    {
        $this->fraudCheckService = $fraudCheckService;
    }

    /**
     * Check courier information for a phone number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkCourierInfo(Request $request): JsonResponse
    {
        $request->validate([
            'phone_number' => 'required|string'
        ]);

        $originalPhone = $request->input('phone_number');
        $phoneNumber = $this->normalizePhoneNumber($originalPhone);

        Log::debug('Fraud check: normalized phone', [
            'original' => $originalPhone,
            'normalized' => $phoneNumber,
        ]);

        $fraudProvider = getSetting('fraudCheckProvider', 'bdcourier');

        Log::debug('Fraud check: provider selected', [
            'provider' => $fraudProvider,
        ]);

        if ($fraudProvider === 'bdcourier') {
            return $this->handleBdCourierCheck($phoneNumber);
        }

        // Use FraudBD (original service)
        return $this->handleFraudBdCheck($phoneNumber);
    }

    /**
     * Normalize phone number for Bangladeshi mobile format
     *
     * @param string $phoneNumber
     * @return string
     */
    private function normalizePhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $digitsOnly = preg_replace('/\D/', '', $phoneNumber);

        // Remove leading country code +88 or 88
        if (str_starts_with($digitsOnly, '88') && strlen($digitsOnly) > 10) {
            $digitsOnly = substr($digitsOnly, 2);
        }

        // Ensure number starts with 0
        if (!str_starts_with($digitsOnly, '0')) {
            $digitsOnly = '0' . $digitsOnly;
        }

        return $digitsOnly;
    }

    /**
     * Handle fraud check using BdCourier direct API integration
     *
     * @param string $phoneNumber
     * @return JsonResponse
     */
    private function handleBdCourierCheck(string $phoneNumber): JsonResponse
    {
        try {
            $manager = new FraudCheckerManager();
            $result = $manager->check($phoneNumber);

            Log::debug('Fraud check: BdCourier raw result', [
                'phone_number' => $phoneNumber,
                'result' => $result,
            ]);

            $allFailed = true;
            $services = ['steadfast', 'pathao', 'redx'];
            foreach ($services as $service) {
                $hasError = isset($result[$service]['error']);
                Log::debug('Fraud check: service status', [
                    'service' => $service,
                    'has_error' => $hasError,
                    'keys' => array_keys($result[$service] ?? []),
                ]);
                if (isset($result[$service]) && !isset($result[$service]['error'])) {
                    $allFailed = false;
                    break;
                }
            }

            if ($allFailed) {
                // Log all errors for each failed service
                foreach ($services as $service) {
                    if (isset($result[$service]['error'])) {
                        Log::error("BD Courier fraud check failed - {$service} courier service error", [
                            'phone_number' => $phoneNumber,
                            'error' => $result[$service]['error'],
                            'message' => $result[$service]['message'] ?? null
                        ]);
                    }
                }

                Log::error('BD Courier fraud check failed - all courier services failed', [
                    'phone_number' => $phoneNumber,
                    'service_results' => $result
                ]);

                // Return success with empty data so frontend shows 0 values
                return response()->json([
                    'success' => true,
                    'message' => 'Courier information unavailable',
                    'data' => []
                ], 200);
            }

            // Transform BdCourier response to match frontend format
            $transformed = $this->transformBdCourierResponse($result);

            Log::debug('Fraud check: transformed response', [
                'phone_number' => $phoneNumber,
                'transformed' => $transformed,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Courier information retrieved successfully',
                'data' => $transformed
            ], 200);

        } catch (\Exception $e) {
            Log::error('BD Courier fraud check exception', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return success with empty data so frontend shows 0 values without error message
            return response()->json([
                'success' => true,
                'message' => 'Courier information unavailable',
                'data' => []
            ], 200);
        }
    }

    /**
     * Handle fraud check using FraudBD API
     *
     * @param string $phoneNumber
     * @return JsonResponse
     */
    private function handleFraudBdCheck(string $phoneNumber): JsonResponse
    {
        $result = $this->fraudCheckService->checkCourierInfo($phoneNumber);

        if ($result['error'] !== null) {
            Log::error('FraudBD fraud check failed', [
                'phone_number' => $phoneNumber,
                'error' => $result['error']
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['error'],
                'data' => null
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Courier information retrieved successfully',
            'data' => $result['result']
        ], 200);
    }

    /**
     * Transform BdCourier response to match frontend format
     *
     * @param array $bdResult
     * @return array
     */
    private function transformBdCourierResponse(array $bdResult): array
    {
        $transformed = [];
        $courierMappings = [
            'steadfast' => 'Steadfast',
            'pathao' => 'Pathao',
            'redx' => 'Redx'
        ];

        foreach ($courierMappings as $key => $displayName) {
            if (!isset($bdResult[$key]) || isset($bdResult[$key]['error'])) {
                continue;
            }

            $data = $bdResult[$key];
            $total = $data['total'] ?? 0;
            $success = $data['success'] ?? 0;
            $cancel = $data['cancel'] ?? 0;
            $successRatio = $data['success_ratio'] ?? 0;
            $customerRating = $data['customer_rating'] ?? null;

            // Calculate offer rate (reject rate)
            $offerRate = $total > 0 ? round(($cancel / $total) * 100, 2) : 0;

            $transformed[$displayName] = [
                'logo' => null, // Placeholder - no logo available from BdCourier
                'data_type' => 'delivery',
                'total' => $total,
                'success' => $success,
                'cancel' => $cancel,
                'orders' => $total,      // Frontend uses 'orders'
                'deliveries' => $success, // Frontend uses 'deliveries'
                'offers' => $cancel,     // Frontend uses 'offers' for rejects
                'offer_rate' => $offerRate,
                'success_rate' => $successRatio,
                'customer_rating' => $customerRating, // Available from Pathao API
                'message' => null,         // Not available from BdCourier
                'risk_level' => null,      // Not available from BdCourier
            ];
        }

        return $transformed;
    }
}

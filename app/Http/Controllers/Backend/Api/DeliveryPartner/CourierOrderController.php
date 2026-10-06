<?php

namespace App\Http\Controllers\Backend\Api\DeliveryPartner;

use App\Data\Orders\PathaoOrderData;
use App\Data\Orders\RedxOrderData;
use App\Data\Orders\SteadfastOrderData;
use App\Http\Controllers\Controller;
use App\Models\OrderGroup;
use App\Services\Delivery\DeliveryPartnerFactory;
use App\Services\Delivery\PathaoCourierService;
use App\Services\Delivery\RedxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourierOrderController extends Controller
{
    public function store(Request $request)
    {
        $partner = strtolower($request->partner); // 'pathao', 'steadfast', 'redx'

        Log::debug('Courier order: store() called', [
            'partner' => $partner,
            'request_all' => $request->all(),
        ]);

        if (! in_array($partner, ['pathao', 'steadfast', 'redx'])) {
            Log::debug('Courier order: unsupported partner', ['partner' => $partner]);

            return response()->json([
                'success' => false,
                'message' => $partner.' is not supported',
            ]);
        }

        try {
            Log::debug('Courier order: creating delivery partner manager', ['partner' => $partner]);
            $manager = DeliveryPartnerFactory::make($partner);

            Log::debug('Courier order: looking up order group', ['order_group_id' => $request->order_group_id]);
            $order_group = OrderGroup::find($request->order_group_id);

            if (is_null($order_group)) {
                Log::warning('Courier order creation failed - order not found', [
                    'partner' => $partner,
                    'order_group_id' => $request->order_group_id,
                ]);

                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => translate('Order not found with this code'),
                ], 200);
            }

            Log::debug('Courier order: order group found', [
                'order_code' => $order_group->code,
                'order_group_id' => $order_group->id,
                'name' => $order_group->name,
                'phone' => $order_group->phone,
                'shipping_address' => $order_group->shipping_address,
                'existing_courier_partner' => $order_group->courier_partner,
            ]);

            $dto = $this->resolveDto($partner, $request, $order_group);

            Log::debug('Courier order: DTO resolved', [
                'partner' => $partner,
                'dto_class' => get_class($dto),
                'dto' => (array) $dto,
            ]);

            Log::debug('Courier order: calling createOrder on partner API', ['partner' => $partner]);
            $response = $manager->createOrder($dto);

            Log::debug('Courier order: createOrder response received', [
                'partner' => $partner,
                'response' => $response,
            ]);

            $trackingNumber = extractTrackingNumber($partner, $response);
            $deliveryStatus = extractDeliveryStatus($partner, $response);

            Log::debug('Courier order: extracted tracking info', [
                'partner' => $partner,
                'tracking_number' => $trackingNumber,
                'delivery_status' => $deliveryStatus,
            ]);

            $order_group->update([
                'courier_partner' => $partner,
                'tracking_number' => $trackingNumber,
                'delivery_status' => $deliveryStatus,
                'courier_response' => $response,
            ]);

            Log::debug('Courier order: order_group updated with courier data', [
                'order_code' => $order_group->code,
                'courier_partner' => $partner,
                'tracking_number' => $trackingNumber,
                'delivery_status' => $deliveryStatus,
            ]);

            Log::info('Courier order created successfully', [
                'partner' => $partner,
                'order_code' => $order_group->code,
                'tracking_number' => $trackingNumber,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'success',
                'partner' => $partner,
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            Log::error('Courier order creation failed', [
                'partner' => $partner,
                'order_code' => $request->code ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'partner' => $partner,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    protected function resolveDto(string $partner, Request $request, OrderGroup $order_group): object
    {
        $transaction = $order_group->transaction;

        Log::debug('Courier order: resolveDto() called', [
            'partner' => $partner,
            'order_code' => $order_group->code,
            'transaction_id' => $transaction?->id,
            'transaction_status' => $transaction?->status,
        ]);

        $isPaid = $transaction && $transaction->status === 'paid';
        $totalAmount = $transaction ? $transaction->total_amount : 0;
        $amountToCollect = $isPaid ? 0 : $totalAmount;

        Log::debug('Courier order: resolveDto() payment info', [
            'partner' => $partner,
            'is_paid' => $isPaid,
            'total_amount' => $totalAmount,
            'amount_to_collect' => $amountToCollect,
        ]);

        return match ($partner) {
            'pathao' => new PathaoOrderData(
                store_id: (int) env('PATHAO_STORE_ID'),
                merchant_order_id: $order_group->code,
                recipient_name: $order_group->name,
                recipient_phone: $order_group->phone,
                recipient_address: $order_group->shipping_address,
                recipient_city: (int) $request->input('recipient_city'),
                recipient_zone: (int) $request->input('recipient_zone'),
                recipient_area: (int) $request->input('recipient_area'),
                delivery_type: (int) ($request->input('delivery_type') ?? 48),
                item_type: (int) ($request->input('item_type') ?? 2),
                item_quantity: (int) $order_group->getTotalQuantity(),
                item_weight: (float) $request->input('item_weight'),
                item_description: $request->input('item_description') ?? '',
                amount_to_collect: (int) $amountToCollect,
                special_instruction: $request->input('instructions') ?? ''
            ),

            'steadfast' => new SteadfastOrderData(
                invoice: $order_group->code,
                recipient_name: $order_group->name,
                recipient_phone: $order_group->phone,
                recipient_email: $order_group->email,
                recipient_address: $order_group->shipping_address,
                cod_amount: (float) $amountToCollect,
                delivery_type: 0, // Home delivery only; point delivery not supported yet
                total_lot: (int) $order_group->getTotalQuantity(),
                note: $request->input('instructions') ?? ''
            ),

            'redx' => new RedxOrderData(
                customer_name: $order_group->name,
                customer_phone: $order_group->phone,
                delivery_area: $request->input('delivery_area') ?? '',
                delivery_area_id: (int) ($request->input('delivery_area_id') ?? 0),
                customer_address: $order_group->shipping_address,
                cash_collection_amount: (string) $amountToCollect,
                parcel_weight: (string) $request->input('weight'),
                merchant_invoice_id: (string) $order_group->code,
                instruction: $request->input('instructions') ?? '',
                type: $request->input('type') ?? 'delivery',
                value: (string) $totalAmount
            ),

            default => throw new \InvalidArgumentException("Unsupported partner: {$partner}"),
        };
    }

    public function getCities()
    {
        try {
            $pathaoService = new PathaoCourierService;
            $cities = $pathaoService->getCities();

            return response()->json(['status' => 'success', 'data' => $cities]);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch Pathao cities in controller', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getZones(int $cityId)
    {
        try {
            $pathaoService = new PathaoCourierService;
            $zones = $pathaoService->getZones($cityId);

            return response()->json(['status' => 'success', 'data' => $zones]);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch Pathao zones in controller', [
                'city_id' => $cityId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getAreas(int $zoneId)
    {
        try {
            $pathaoService = new PathaoCourierService;
            $areas = $pathaoService->getAreas($zoneId);

            return response()->json(['status' => 'success', 'data' => $areas]);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch Pathao areas in controller', [
                'zone_id' => $zoneId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getRedxAreas(RedxService $redxService)
    {
        try {
            $areas = $redxService->getAreas();

            return response()->json(['status' => 'success', 'data' => $areas]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getDeliveryStatus(Request $request)
    {
        $partner = strtolower($request->partner);
        $trackingId = $request->tracking_id;

        if (! in_array($partner, ['pathao', 'steadfast', 'redx'])) {
            return response()->json([
                'success' => false,
                'message' => "{$partner} is not supported",
            ]);
        }

        if (! $trackingId) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking ID is required',
            ], 422);
        }

        try {
            $manager = DeliveryPartnerFactory::make($partner);
            $response = $manager->getDeliveryStatus($trackingId);

            return response()->json([
                'success' => true,
                'partner' => $partner,
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'partner' => $partner,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkStore(Request $request)
    {
        $partner = strtolower($request->partner);

        Log::debug('Bulk courier order: bulkStore() called', [
            'partner' => $partner,
            'request_all' => $request->all(),
        ]);

        if (! in_array($partner, ['pathao', 'steadfast', 'redx'])) {
            Log::debug('Bulk courier order: unsupported partner', ['partner' => $partner]);

            return response()->json([
                'success' => false,
                'message' => $partner.' is not supported',
            ]);
        }

        $ordersConfig = $request->input('orders', []);
        $orderGroupIds = collect($ordersConfig)->pluck('order_group_id')->filter()->toArray();

        if (empty($orderGroupIds)) {
            $orderGroupIds = $request->input('order_group_ids', []);
        }

        Log::debug('Bulk courier order: parsed order group ids', [
            'partner' => $partner,
            'order_group_ids' => $orderGroupIds,
            'orders_config_count' => count($ordersConfig),
        ]);

        if (empty($orderGroupIds)) {
            Log::debug('Bulk courier order: no orders selected');

            return response()->json([
                'success' => false,
                'message' => 'No orders selected',
            ], 422);
        }

        $orderGroups = OrderGroup::whereIn('id', $orderGroupIds)->get();
        $alreadyAssigned = [];
        $ordersToProcess = [];
        $skipped = [];

        Log::debug('Bulk courier order: fetched order groups from DB', [
            'requested_ids' => count($orderGroupIds),
            'found_in_db' => $orderGroups->count(),
        ]);

        $configById = collect($ordersConfig)->keyBy('order_group_id')->toArray();

        foreach ($orderGroups as $orderGroup) {
            if (! empty($orderGroup->courier_partner)) {
                Log::debug('Bulk courier order: skipping already assigned', [
                    'order_group_id' => $orderGroup->id,
                    'existing_partner' => $orderGroup->courier_partner,
                ]);
                $alreadyAssigned[] = [
                    'code' => $orderGroup->code,
                    'reason' => 'Already assigned to '.$orderGroup->courier_partner,
                ];
            } else {
                $config = $configById[$orderGroup->id] ?? null;

                if ($config) {
                    $validationErrors = $this->validateSingleOrderConfig($partner, $config);
                    if (! empty($validationErrors)) {
                        Log::debug('Bulk courier order: skipping due to validation errors', [
                            'order_group_id' => $orderGroup->id,
                            'validation_errors' => $validationErrors,
                        ]);
                        $skipped[] = [
                            'code' => $orderGroup->code,
                            'reason' => 'Missing: '.implode(', ', $validationErrors),
                        ];

                        continue;
                    }
                }

                Log::debug('Bulk courier order: order queued for processing', [
                    'order_group_id' => $orderGroup->id,
                    'has_per_order_config' => ! empty($config),
                ]);
                $ordersToProcess[] = [
                    'order_group' => $orderGroup,
                    'config' => $config,
                ];
            }
        }

        Log::debug('Bulk courier order: pre-processing summary', [
            'to_process' => count($ordersToProcess),
            'already_assigned' => count($alreadyAssigned),
            'skipped' => count($skipped),
        ]);

        $success = [];
        $failed = [];

        try {
            Log::debug('Bulk courier order: creating delivery partner manager', ['partner' => $partner]);
            $manager = DeliveryPartnerFactory::make($partner);

            foreach ($ordersToProcess as $index => $orderData) {
                $orderGroup = $orderData['order_group'];
                $config = $orderData['config'];

                Log::debug('Bulk courier order: processing order', [
                    'index' => $index,
                    'order_code' => $orderGroup->code,
                    'has_per_order_config' => ! empty($config),
                ]);

                try {
                    if ($config) {
                        Log::debug('Bulk courier order: resolving DTO from per-order config', [
                            'order_code' => $orderGroup->code,
                            'config' => $config,
                        ]);
                        $dto = $this->resolveDtoFromArray($partner, $config, $orderGroup);
                    } else {
                        Log::debug('Bulk courier order: resolving DTO from request (fallback)', [
                            'order_code' => $orderGroup->code,
                        ]);
                        $dto = $this->resolveDto($partner, $request, $orderGroup);
                    }

                    Log::debug('Bulk courier order: DTO resolved', [
                        'order_code' => $orderGroup->code,
                        'dto_class' => get_class($dto),
                    ]);

                    Log::debug('Bulk courier order: calling createOrder', [
                        'order_code' => $orderGroup->code,
                        'partner' => $partner,
                    ]);
                    $response = $manager->createOrder($dto);

                    Log::debug('Bulk courier order: createOrder response', [
                        'order_code' => $orderGroup->code,
                        'response' => $response,
                    ]);

                    $trackingNumber = extractTrackingNumber($partner, $response);
                    $deliveryStatus = extractDeliveryStatus($partner, $response);

                    Log::debug('Bulk courier order: extracted tracking info', [
                        'order_code' => $orderGroup->code,
                        'tracking_number' => $trackingNumber,
                        'delivery_status' => $deliveryStatus,
                    ]);

                    $orderGroup->update([
                        'courier_partner' => $partner,
                        'tracking_number' => $trackingNumber,
                        'delivery_status' => $deliveryStatus,
                        'courier_response' => $response,
                    ]);

                    Log::debug('Bulk courier order: order_group updated', [
                        'order_code' => $orderGroup->code,
                        'tracking_number' => $trackingNumber,
                    ]);

                    $success[] = [
                        'code' => $orderGroup->code,
                        'tracking_number' => $trackingNumber,
                        'status' => $deliveryStatus,
                    ];
                } catch (\Throwable $e) {
                    Log::debug('Bulk courier order: individual order failed', [
                        'order_code' => $orderGroup->code,
                        'error' => $e->getMessage(),
                    ]);
                    $failed[] = [
                        'code' => $orderGroup->code,
                        'reason' => $e->getMessage(),
                    ];
                }
            }

            // Add pre-filtered orders to failed list
            foreach ($alreadyAssigned as $item) {
                $failed[] = $item;
            }

            $submittedCount = count($ordersToProcess);

            // Log summary
            Log::info('Bulk courier order processing completed', [
                'partner' => $partner,
                'total_selected' => count($orderGroupIds),
                'submitted' => $submittedCount,
                'skipped' => count($skipped),
                'success_count' => count($success),
                'failed_count' => count($failed),
                'already_assigned_count' => count($alreadyAssigned),
                'failed_orders' => $failed,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'completed',
                'partner' => $partner,
                'summary' => [
                    'total_selected' => count($orderGroupIds),
                    'submitted' => $submittedCount,
                    'skipped' => count($skipped),
                    'already_assigned' => count($alreadyAssigned),
                    'success' => count($success),
                    'failed' => count($failed),
                ],
                'data' => [
                    'success' => $success,
                    'skipped' => $skipped,
                    'failed' => $failed,
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('Bulk courier order processing failed', [
                'partner' => $partner,
                'order_group_ids' => $orderGroupIds,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'partner' => $partner,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Validate orders configuration for the selected partner
     */
    protected function validateOrdersConfig(string $partner, array $ordersConfig): array
    {
        $errors = [];

        foreach ($ordersConfig as $index => $config) {
            $orderErrors = [];

            // Common validations
            if (empty($config['order_group_id'])) {
                $orderErrors[] = 'Order group ID is required';
            }

            if (! isset($config['weight']) || (float) $config['weight'] <= 0) {
                $orderErrors[] = 'Weight is required and must be greater than 0';
            }

            // Partner-specific validations
            switch ($partner) {
                case 'pathao':
                    if (empty($config['recipient_city'])) {
                        $orderErrors[] = 'City is required';
                    }
                    if (empty($config['recipient_zone'])) {
                        $orderErrors[] = 'Zone is required';
                    }
                    if (empty($config['recipient_area'])) {
                        $orderErrors[] = 'Area is required';
                    }
                    break;

                case 'redx':
                    if (empty($config['delivery_area_id'])) {
                        $orderErrors[] = 'Area is required';
                    }
                    break;
            }

            if (! empty($orderErrors)) {
                $errors[$config['order_group_id'] ?? "order_{$index}"] = $orderErrors;
            }
        }

        return $errors;
    }

    /**
     * Resolve DTO from array configuration (per-order config)
     */
    protected function resolveDtoFromArray(string $partner, array $config, OrderGroup $order_group): object
    {
        $transaction = $order_group->transaction;

        Log::debug('Courier order: resolveDtoFromArray() called', [
            'partner' => $partner,
            'order_code' => $order_group->code,
            'config' => $config,
            'transaction_id' => $transaction?->id,
            'transaction_status' => $transaction?->status,
        ]);

        $isPaid = $transaction && $transaction->status === 'paid';
        $totalAmount = $transaction ? $transaction->total_amount : 0;
        $amountToCollect = $isPaid ? 0 : $totalAmount;

        Log::debug('Courier order: resolveDtoFromArray() payment info', [
            'partner' => $partner,
            'is_paid' => $isPaid,
            'total_amount' => $totalAmount,
            'amount_to_collect' => $amountToCollect,
        ]);

        return match ($partner) {
            'pathao' => new PathaoOrderData(
                store_id: (int) env('PATHAO_STORE_ID'),
                merchant_order_id: $order_group->code,
                recipient_name: $order_group->name,
                recipient_phone: $order_group->phone,
                recipient_address: $order_group->shipping_address,
                recipient_city: (int) $config['recipient_city'],
                recipient_zone: (int) $config['recipient_zone'],
                recipient_area: (int) $config['recipient_area'],
                delivery_type: (int) ($config['delivery_type'] ?? 48),
                item_type: (int) ($config['item_type'] ?? 2),
                item_quantity: (int) $order_group->getTotalQuantity(),
                item_weight: (float) $config['weight'],
                item_description: $config['item_description'] ?? '',
                amount_to_collect: (int) $amountToCollect,
                special_instruction: $config['instructions'] ?? ''
            ),

            'steadfast' => new SteadfastOrderData(
                invoice: $order_group->code,
                recipient_name: $order_group->name,
                recipient_phone: $order_group->phone,
                recipient_email: $order_group->email,
                recipient_address: $order_group->shipping_address,
                cod_amount: (float) $amountToCollect,
                delivery_type: 0, // Home delivery only; point delivery not supported yet
                total_lot: (int) $order_group->getTotalQuantity(),
                note: $config['instructions'] ?? ''
            ),

            'redx' => new RedxOrderData(
                customer_name: $order_group->name,
                customer_phone: $order_group->phone,
                delivery_area: $config['delivery_area'] ?? '',
                delivery_area_id: (int) ($config['delivery_area_id'] ?? 0),
                customer_address: $order_group->shipping_address,
                cash_collection_amount: (string) $amountToCollect,
                parcel_weight: (string) $config['weight'],
                merchant_invoice_id: (string) $order_group->code,
                instruction: $config['instructions'] ?? '',
                type: $config['type'] ?? 'delivery',
                value: (string) $totalAmount
            ),

            default => throw new \InvalidArgumentException("Unsupported partner: {$partner}"),
        };
    }

    /**
     * Validate a single order's configuration
     */
    protected function validateSingleOrderConfig(string $partner, array $config): array
    {
        $errors = [];

        // Common validations
        if (empty($config['order_group_id'])) {
            $errors[] = 'Order group ID';
        }

        if (! isset($config['weight']) || (float) $config['weight'] <= 0) {
            $errors[] = 'Weight';
        }

        // Partner-specific validations
        switch ($partner) {
            case 'pathao':
                if (empty($config['recipient_city'])) {
                    $errors[] = 'City';
                }
                if (empty($config['recipient_zone'])) {
                    $errors[] = 'Zone';
                }
                if (empty($config['recipient_area'])) {
                    $errors[] = 'Area';
                }
                break;

            case 'redx':
                if (empty($config['delivery_area_id'])) {
                    $errors[] = 'Area';
                }
                break;
        }

        return $errors;
    }
}

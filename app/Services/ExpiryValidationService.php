<?php

namespace App\Services;

use App\Models\Stock;
use Carbon\Carbon;

class ExpiryValidationService
{
    /**
     * Validate expiry date for stock entries
     */
    public static function validateExpiryDate($expiryDate, $context = 'purchase')
    {
        $data = [
            'status' => 200,
            'message' => '',
            'warnings' => [],
            'errors' => [],
        ];

        if (empty($expiryDate)) {
            return $data; // Expiry date is optional
        }

        try {
            $expiryCarbon = Carbon::parse($expiryDate);
            $today = Carbon::today();

            // Check if the date is in the past (allow today as valid)
            if ($expiryCarbon->isPast() && !$expiryCarbon->isSameDay($today)) {
                $data['status'] = 403;
                $data['errors'][] = 'Expiry date cannot be in the past';
                return $data;
            }

            // Check if the item is expiring soon (within 30 days)
            $daysUntilExpiry = $today->diffInDays($expiryCarbon, false);
            if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
                $data['warnings'][] = 'This item is expiring in ' . $daysUntilExpiry . ' days';
            }

            // For purchase orders, check if the date is reasonable (not too far in future)
            if ($context === 'purchase' && $daysUntilExpiry > 365) {
                $data['warnings'][] = 'This expiry date is more than 1 year in the future';
            }

        } catch (\Exception $e) {
            $data['status'] = 403;
            $data['errors'][] = 'Invalid expiry date format';
        }

        return $data;
    }

    /**
     * Get expiry status for display with styling information
     */
    public static function getExpiryStatusInfo($expiryDate)
    {
        if (!$expiryDate) {
            return [
                'status' => 'no-expiry',
                'label' => translate('No Expiry'),
                'color' => 'gray',
                'icon' => 'fa-infinity'
            ];
        }

        try {
            $expiryCarbon = Carbon::parse($expiryDate);
            $today = Carbon::today();
            $daysUntilExpiry = $today->diffInDays($expiryCarbon, false);

            if ($daysUntilExpiry < 0) {
                return [
                    'status' => 'expired',
                    'label' => translate('Expired'),
                    'color' => 'red',
                    'icon' => 'fa-exclamation-triangle'
                ];
            } elseif ($daysUntilExpiry <= 30) {
                return [
                    'status' => 'expiring-soon',
                    'label' => 'Expiring in ' . $daysUntilExpiry . ' days',
                    'color' => 'orange',
                    'icon' => 'fa-clock'
                ];
            } elseif ($daysUntilExpiry <= 90) {
                return [
                    'status' => 'expiring-medium',
                    'label' => 'Expiring in ' . $daysUntilExpiry . ' days',
                    'color' => 'yellow',
                    'icon' => 'fa-clock'
                ];
            } else {
                return [
                    'status' => 'fresh',
                    'label' => $expiryCarbon->format('Y-m-d'),
                    'color' => 'green',
                    'icon' => 'fa-check-circle'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'invalid-date',
                'label' => translate('Invalid Date'),
                'color' => 'red',
                'icon' => 'fa-times-circle'
            ];
        }
    }

    /**
     * Get stock recommendations for FIFO (First-In-First-Out)
     */
    public static function getFifoRecommendations($productVariationId, $warehouseId, $quantityNeeded)
    {
        $stocks = Stock::where('product_variation_id', $productVariationId)
            ->where('warehouse_id', $warehouseId)
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->where('qty', '>', 0)
            ->get();

        $recommendations = [];
        $remainingQuantity = $quantityNeeded;

        foreach ($stocks as $stock) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $expiryInfo = self::getExpiryStatusInfo($stock->expiry_date);
            $quantityToUse = min($stock->qty, $remainingQuantity);

            $recommendations[] = [
                'stock_id' => $stock->id,
                'quantity' => $quantityToUse,
                'expiry_date' => $stock->expiry_date,
                'expiry_info' => $expiryInfo
            ];

            $remainingQuantity -= $quantityToUse;
        }

        return [
            'recommendations' => $recommendations,
            'sufficient_stock' => $remainingQuantity <= 0,
            'shortfall' => max(0, $remainingQuantity)
        ];
    }

    /**
     * Check for expired stock and generate reports
     */
    public static function getExpiredStockReport($warehouseId = null)
    {
        $query = Stock::whereNotNull('expiry_date')
            ->where('expiry_date', '<', strtotime(date('Y-m-d')))
            ->where('qty', '>', 0);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $expiredStocks = $query->with(['productVariation.product', 'warehouse'])->get();

        $report = [
            'total_expired_items' => $expiredStocks->count(),
            'total_expired_quantity' => $expiredStocks->sum('qty'),
            'total_value' => $expiredStocks->sum(function($stock) {
                return $stock->qty * $stock->purchase_cost;
            }),
            'items' => []
        ];

        foreach ($expiredStocks as $stock) {
            $report['items'][] = [
                'id' => $stock->id,
                'product_name' => $stock->productVariation->product->collectTranslation('name'),
                'variation' => $stock->productVariation->code,
                'warehouse' => $stock->warehouse->name,
                'quantity' => $stock->qty,
                'expiry_date' => date('Y-m-d', $stock->expiry_date),
                'days_expired' => abs(Carbon::createFromTimestamp($stock->expiry_date)->diffInDays(now())),
                'value' => $stock->qty * $stock->purchase_cost
            ];
        }

        return $report;
    }

    /**
     * Get stock expiring soon report
     */
    public static function getExpiringSoonReport($days = 30, $warehouseId = null)
    {
        $futureDate = strtotime(date('Y-m-d', strtotime("+$days days")));

        $query = Stock::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', strtotime(date('Y-m-d')))
            ->where('expiry_date', '<=', $futureDate)
            ->where('qty', '>', 0);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $expiringStocks = $query->with(['productVariation.product', 'warehouse'])
            ->orderBy('expiry_date', 'asc')
            ->get();

        $report = [
            'total_expiring_items' => $expiringStocks->count(),
            'total_expiring_quantity' => $expiringStocks->sum('qty'),
            'total_value' => $expiringStocks->sum(function($stock) {
                return $stock->qty * $stock->purchase_cost;
            }),
            'items' => []
        ];

        foreach ($expiringStocks as $stock) {
            $daysUntilExpiry = Carbon::createFromTimestamp($stock->expiry_date)->diffInDays(now());
            $expiryInfo = self::getExpiryStatusInfo($stock->expiry_date);

            $report['items'][] = [
                'id' => $stock->id,
                'product_name' => $stock->productVariation->product->collectTranslation('name'),
                'variation' => $stock->productVariation->code,
                'warehouse' => $stock->warehouse->name,
                'quantity' => $stock->qty,
                'expiry_date' => date('Y-m-d', $stock->expiry_date),
                'days_until_expiry' => $daysUntilExpiry,
                'expiry_status' => $expiryInfo,
                'value' => $stock->qty * $stock->purchase_cost
            ];
        }

        return $report;
    }
}
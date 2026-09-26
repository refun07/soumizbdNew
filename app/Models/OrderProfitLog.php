<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProfitLog extends Model
{
    use HasFactory;

    protected $table = 'order_profit_logs';

    protected $guarded = [];

    protected $casts = [
        'agent_commission' => 'double',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function calculateForOrder(Order $order): self
    {
        $order->loadMissing(['orderItems', 'commissionHistory', 'agentCommissionHistory']);

        $revenue = $order->total_amount;

        $cogs = $order->orderItems->sum('total_cost');

        $returnedItems = ReturnRequestItem::whereHas('returnRequest', function ($q) use ($order) {
            $q->where('order_id', $order->id)->where('status', 4);
        })->get();

        $returnedRevenue = $returnedItems->sum(fn ($i) => $i->price * $i->quantity);

        $returnedCogs = 0;
        foreach ($returnedItems as $item) {
            $orderItem = $order->orderItems->firstWhere('product_variation_id', $item->product_variation_id);
            if ($orderItem && $orderItem->qty > 0) {
                $returnedCogs += ($orderItem->total_cost / $orderItem->qty) * $item->quantity;
            }
        }

        $revenue -= $returnedRevenue;
        $cogs -= $returnedCogs;

        $adminCommission = $order->commissionHistory->admin_earning_amount ?? 0;
        $sellerEarning = $order->commissionHistory->shop_earning_amount ?? 0;
        $agentCommission = $order->agentCommissionHistory->commission_amount ?? 0;
        $shippingCost = $order->shipping_charge_amount;
        $taxCollected = $order->tax_amount;
        $discountGiven = $order->discount_amount + $order->coupon_discount_amount + $order->admin_discount_amount;

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $adminCommission - $agentCommission;
        $profitMarginPct = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        if ($order->delivery_status === 'cancelled') {
            $grossProfit = 0;
            $netProfit = 0;
            $profitMarginPct = 0;
        }

        return self::updateOrCreate(
            ['order_id' => $order->id],
            [
                'revenue' => $revenue,
                'cogs' => $cogs,
                'admin_commission' => $adminCommission,
                'seller_earning' => $sellerEarning,
                'agent_commission' => $agentCommission,
                'shipping_cost' => $shippingCost,
                'tax_collected' => $taxCollected,
                'discount_given' => $discountGiven,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
                'profit_margin_pct' => round($profitMarginPct, 2),
            ]
        );
    }
}

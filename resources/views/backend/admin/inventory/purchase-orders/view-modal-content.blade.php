@php
    $returnOrder = $purchaseOrder->return;
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-4">
            <p class="text-xs text-muted">{{ translate('Reference') }}</p>
            <p class="font-semibold">#PO-{{ $purchaseOrder->reference_code }}</p>
        </div>
        <div class="col-span-12 md:col-span-4">
            <p class="text-xs text-muted">{{ translate('Date') }}</p>
            <p class="font-semibold">{{ date('d M, Y', $purchaseOrder->date) }}</p>
        </div>
        <div class="col-span-12 md:col-span-4">
            <p class="text-xs text-muted">{{ translate('Warehouse') }}</p>
            <p class="font-semibold">{{ $purchaseOrder->warehouse?->name ?? '-' }}</p>
        </div>
        <div class="col-span-12 md:col-span-4">
            <p class="text-xs text-muted">{{ translate('Supplier') }}</p>
            <p class="font-semibold">{{ $purchaseOrder->supplier?->name ?? '-' }}</p>
        </div>
        <div class="col-span-12 md:col-span-4">
            <p class="text-xs text-muted">{{ translate('Status') }}</p>
            <p class="font-semibold capitalize">{{ $purchaseOrder->status }}</p>
            @if ($returnOrder)
                <p class="text-xs mt-1 text-orange-500">{{ translate('Returned') }}</p>
            @endif
        </div>
        <div class="col-span-12 md:col-span-4">
            <p class="text-xs text-muted">{{ translate('Payment Status') }}</p>
            <p class="font-semibold capitalize">{{ $purchaseOrder->payment_status }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-border rounded-md">
            <thead class="bg-primary/10">
                <tr>
                    <th class="text-left px-3 py-2">{{ translate('Product') }}</th>
                    <th class="text-right px-3 py-2">{{ translate('Unit Price') }}</th>
                    <th class="text-center px-3 py-2">{{ translate('Qty') }}</th>
                    <th class="text-right px-3 py-2">{{ translate('Tax') }}</th>
                    <th class="text-right px-3 py-2">{{ translate('Discount') }}</th>
                    <th class="text-right px-3 py-2">{{ translate('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchaseOrder->orders as $item)
                    @php
                        $product = $item->productVariation?->product;
                        $variationName = '';
                        if ($item->productVariation?->code) {
                            $variationName = generateVariationName($item->productVariation->code);
                        }
                    @endphp
                    <tr class="border-t border-border">
                        <td class="px-3 py-2">
                            <p>{{ $product?->CollectTranslation('name') ?? translate('Product not found') }}</p>
                            @if ($variationName)
                                <p class="text-xs text-muted">{{ $variationName }}</p>
                            @endif
                        </td>
                        <td class="text-right px-3 py-2">{{ formatPrice($item->unit_price) }}</td>
                        <td class="text-center px-3 py-2">{{ $item->qty }}</td>
                        <td class="text-right px-3 py-2">{{ formatPrice($item->tax) }}</td>
                        <td class="text-right px-3 py-2">{{ formatPrice($item->discount) }}</td>
                        <td class="text-right px-3 py-2">{{ formatPrice($item->grand_total) }}</td>
                    </tr>
                @empty
                    <tr class="border-t border-border">
                        <td colspan="6" class="px-3 py-3 text-center text-muted">
                            {{ translate('No products found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-12 gap-3">
        <div class="col-span-12 md:col-span-6 border border-border rounded-md p-3">
            <p class="flex items-center justify-between text-sm mb-2">
                <span>{{ translate('Sub Total') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->sub_total) }}</span>
            </p>
            <p class="flex items-center justify-between text-sm mb-2">
                <span>{{ translate('Tax') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->tax_value) }}</span>
            </p>
            <p class="flex items-center justify-between text-sm mb-2">
                <span>{{ translate('Shipping') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->shipping) }}</span>
            </p>
            <p class="flex items-center justify-between text-sm mb-2">
                <span>{{ translate('Discount') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->discount) }}</span>
            </p>
            <p class="flex items-center justify-between text-sm mb-2">
                <span>{{ translate('Grand Total') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->grand_total) }}</span>
            </p>
            <p class="flex items-center justify-between text-sm mb-2 text-green-600">
                <span>{{ translate('Paid') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->paid) }}</span>
            </p>
            <p class="flex items-center justify-between text-sm text-red-600">
                <span>{{ translate('Due') }}</span>
                <span class="font-semibold">{{ formatPrice($purchaseOrder->due) }}</span>
            </p>
        </div>

        <div class="col-span-12 md:col-span-6 border border-border rounded-md p-3">
            <h4 class="font-semibold mb-2">{{ translate('Payments') }}</h4>
            <div class="max-h-52 overflow-y-auto">
                @forelse ($purchaseOrder->payments as $payment)
                    <div class="border border-border rounded-md p-2 mb-2 text-sm">
                        <p class="flex items-center justify-between">
                            <span>{{ $payment->date ? date('d M, Y', strtotime($payment->date)) : '-' }}</span>
                            <span class="font-semibold">{{ formatPrice($payment->paid_amount) }}</span>
                        </p>
                        <p class="text-xs text-muted mt-1">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? '')) }}
                        </p>
                        @if ($payment->note)
                            <p class="text-xs mt-1">{{ $payment->note }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-muted">{{ translate('No payments found') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    @if (!empty($purchaseOrder->note))
        <div class="border border-border rounded-md p-3">
            <h4 class="font-semibold mb-1">{{ translate('Note') }}</h4>
            <p class="text-sm">{{ $purchaseOrder->note }}</p>
        </div>
    @endif
</div>

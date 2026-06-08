<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="uppercase text-left bg-primary/10">
            <tr>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">#</th>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ translate('Product') }}</th>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ translate('Current Stock') }}</th>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ translate('Quantity') }}</th>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ translate('Damage Type') }}</th>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ translate('Reason') }}</th>
                <th class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ translate('Action') }}</th>
            </tr>
        </thead>
        <tbody class="damage-tbody">
            @if (isset($damageStock))
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">1</td>
                    <td class="px-6 py-4 text-sm text-foreground font-light">
                        {{ $damageStock->productVariation?->product?->collectTranslation('name') }}
                        @if ($damageStock->productVariation?->product?->has_variation)
                            - <span class="text-orange-500">{{ $damageStock->productVariation?->name }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                        {{ $damageStock->productVariation?->productVariationStocks?->sum('stock_qty') ?? 0 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $damageStock->quantity }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                        {{ translate(ucfirst(str_replace('_', ' ', $damageStock->damage_type))) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">{{ $damageStock->reason ?? translate('N/A') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">-</td>
                </tr>
            @else
                <tr class="bg-background no-data">
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                        {{ translate('No data') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

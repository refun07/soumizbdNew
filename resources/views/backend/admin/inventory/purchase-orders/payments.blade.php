<div class="purchase-orders">
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:mx-0.5 lg:mx-0.5">
            <div class="inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-primary/10 border-b border-border">
                            <tr class="text-left">
                                <th class="p-4 ps-6 w-[30px]">
                                    #
                                </th>

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Date') }}
                                </th>

                                <th class="p-4" data-breakpoints="xs sm">
                                    {{ translate('Payment Method') }}
                                </th>

                                <th class="p-4 text-end" data-breakpoints="xs sm">
                                    {{ translate('Amount') }}
                                </th>

                            </tr>
                        </thead>
                        <tbody class="purchase-orders-tbody">
                            @if (count($payments) > 0)
                                @foreach ($payments as $key => $payment)
                                    <tr
                                        class="bg-background border-b border-border transition duration-300 ease-in-out hover:bg-background-hover">

                                        <td class=" w-[30px] text-sm text-foreground font-light px-6 py-4tr-name">
                                            {{ $key + 1 }}
                                        </td>

                                        <td
                                            class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap tr-default-unit-price">
                                            {{ date('d M, Y', $payment->date) }}
                                        </td>

                                        <td
                                            class="text-sm text-foreground font-light px-6 py-4 whitespace-nowrap tr-default-unit-price">
                                            {{ $payment->payment_method }}
                                        </td>

                                        <td
                                            class="text-end text-sm text-foreground font-light px-6 py-4 whitespace-nowrap tr-default-unit-price">
                                            {{ formatPrice($payment->paid_amount) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="bg-background no-data">
                                    <td colspan="9"
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground text-center">
                                        {{ translate('No data') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if (count($orders) > 0)
    @foreach ($orders as $key => $order)
        <a class="cursor-pointer po-product block py-4 px-6 hover:bg-primary/10" onclick="handleOrderClick(this)"
            data-order-id="{{ $order->id }}" data-code="{{ $order->order_code }}">
            {{ $order->order_code }}
        </a>
    @endforeach

    @if ($orders->hasMorePages())
        <div class="text-center p-5">
            <button type="button" class="btn button--warning rounded p-2"
                onclick="loadMoreOrders(this, '{{ $orders->nextPageUrl() }}')">
                <div class="flex items-center">
                    <!-- Inner Ring -->
                    <div
                        class="w-[14px] h-[14px] rounded-full animate-spin
                border-2 border-solid border-white border-t-transparent hidden load-more-spin">
                    </div>
                    <div class="ms-2">{{ translate('Load more..') }}</div>
                </div>
            </button>
        </div>
    @endif
@else
    {{-- no results --}}
    <div class="flex justify-center items-center py-[35px]">
        {{ translate('No results') }}
    </div>
@endif

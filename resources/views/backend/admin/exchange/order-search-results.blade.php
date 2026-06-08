@if (count($orders) > 0)
    @foreach ($orders as $order)
        <a class="cursor-pointer block py-4 px-6 hover:bg-primary/10 exchange-order-result"
            data-code="{{ $order->order_code }}">
            {{ getSetting('orderCodePrefix') }}{{ $order->order_code }}
        </a>
    @endforeach

    @if ($orders->hasMorePages())
        <div class="text-center p-5">
            <button type="button" class="btn button--warning rounded p-2" data-next-url="{{ $orders->nextPageUrl() }}">
                {{ translate('Load more..') }}
            </button>
        </div>
    @endif
@else
    <div class="flex justify-center items-center py-[35px]">
        {{ translate('No results') }}
    </div>
@endif

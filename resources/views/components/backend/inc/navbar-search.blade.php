@if (count($products) == 0 && count($orders) == 0)
    <a href="javascript:void(0);" class="block py-4 px-6 text-center">
        {{ translate('No result') }}
    </a>
@else
    @if (count($products) != 0)
        <div class="flex justify-between py-4 px-6 bg-primary/[.03] text-muted font-bold uppercase">
            <div>
                {{ translate('Products') }}
            </div>
            <a href="{{ route(routePrefix() . '.products.index') . '?search=' . $searchKey }}">
                {{ translate('View More') }}
            </a>
        </div>
        @foreach ($products as $product)
            <a href="{{ route(routePrefix() . '.products.edit', $product->id) }}?lang_key={{ config('app.default_language') }}&translate"
                class="block py-4 px-6">
                {{ $product->collectTranslation('name') }}
            </a>
        @endforeach
    @endif

    @if (count($orders) != 0)
        <div class="flex justify-between py-4 px-6 bg-primary/[.03] text-muted font-bold uppercase">
            <div>
                {{ translate('Orders') }}
            </div>
            <a href="{{ route(routePrefix() . '.orders.index') . '?search=' . $searchKey }}">
                {{ translate('View More') }}
            </a>
        </div>
        @foreach ($orders as $order)
            <a href="{{ route(routePrefix() . '.orders.show', $order->order_code) }}" class="block py-4 px-6">
                {{ $order->user->name }} - {{ $order->order_code }}
            </a>
        @endforeach
    @endif


@endif

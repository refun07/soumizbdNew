<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Successful - {{ $summary['orderCode'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Facebook Pixel - Purchase Event --}}
    @if(config('app.FACEBOOK_PIXEL_ACTIVATION') == '1' && config('app.META_PIXEL_ID'))
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ config('app.META_PIXEL_ID') }}');
        fbq('track', 'PageView');
        fbq('track', 'Purchase', {
            value: {{ $orderGroup->transaction->total_amount ?? 0 }},
            currency: '{{ getSetting('currencyCode') ?? 'USD' }}'
        });
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ config('app.META_PIXEL_ID') }}&ev=Purchase&noscript=1"/>
    </noscript>
    @endif
    {{-- End Facebook Pixel Code --}}
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Success Header -->
            <div class="bg-green-600 py-8 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-4xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">Thank You for Your Order!</h1>
                <p class="text-green-100 mt-2">Your order has been placed successfully</p>
            </div>

            <!-- Order Details -->
            <div class="p-8">
                <!-- Order Code -->
                <div class="text-center mb-6">
                    <p class="text-gray-500 text-sm">Order Number</p>
                    <p class="text-xl font-mono font-bold text-gray-800">{{ $summary['orderCode'] }}</p>
                </div>

                <!-- Customer Info -->
                <div class="border-t border-b border-gray-200 py-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Name</p>
                            <p class="font-medium">{{ $summary['customerName'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Phone</p>
                            <p class="font-medium">{{ $summary['phone'] }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-medium">{{ $summary['email'] ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Payment Method</p>
                            <p class="font-medium">{{ $summary['paymentMethodToShow'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-gray-500 text-sm">Shipping Address</p>
                        <p class="font-medium">{{ $summary['shippingAddress'] }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h3>
                    @foreach($orders as $order)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Seller: {{ $order->shop->name ?? 'Unknown' }}</p>
                            @foreach($order->orderItems as $item)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $variationImage = $item->productVariation->image ?? null;
                                            $productImage = $item->productVariation->product->thumbnail_image ?? null;
                                            $variationImageUrl = $variationImage ? uploadedAsset($variationImage) : '';
                                            $productImageUrl = $productImage ? uploadedAsset($productImage) : '';
                                        @endphp
                                        @if($variationImageUrl)
                                            <img src="{{ $variationImageUrl }}" alt="{{ $item->productVariation->product->name }}" class="w-12 h-12 object-cover rounded">
                                        @elseif($productImageUrl)
                                            <img src="{{ $productImageUrl }}" alt="{{ $item->productVariation->product->name }}" class="w-12 h-12 object-cover rounded">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item->productVariation->product->name }}</p>
                                            <p class="text-sm text-gray-500">Qty: {{ $item->qty }}</p>
                                        </div>
                                    </div>
                                    <p class="font-medium">${{ number_format($item->total_price, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Order Total -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total Amount Paid</span>
                        <span class="text-2xl font-bold text-green-600">${{ number_format($orderGroup->transaction->total_amount ?? 0, 2) }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex gap-4">
                    <a href="/" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

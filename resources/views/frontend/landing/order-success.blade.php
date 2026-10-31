<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Successful - {{ $summary['orderCode'] }}</title>
    @vite('resources/frontend/index.css')
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
<body class="bg-gray-50 bot-order-confirmation">
    <style>
        .bot-order-confirmation { font-family: Arial, sans-serif; color: #202839; font-size: 17px; line-height: 1.6; background: #f4f8f3; }
        .bot-order-confirmation .max-w-2xl { border-radius: 20px; border: 1px solid #dce7df; box-shadow: none; }
        .bot-order-confirmation .bg-green-600 { background: #087c3e; }
        .bot-order-confirmation .text-green-600 { color: #087c3e; }
        .bot-order-confirmation h1 { font-size: clamp(26px, 5vw, 32px); line-height: 1.3; padding-inline: 16px; }
        .bot-order-confirmation h3 { font-size: 20px; }
        .bot-order-confirmation p { overflow-wrap: anywhere; }
        .bot-order-confirmation a { min-height: 46px; border-radius: 999px; }
        .bot-order-confirmation a[href="/"] { background: #087c3e; color: white; }
        .bot-order-confirmation a:focus-visible { outline: 3px solid #b78335; outline-offset: 3px; }
        @media (max-width: 480px) {
            .bot-order-confirmation .p-8 { padding: 24px 16px; }
            .bot-order-confirmation .py-12 { padding-block: 24px; }
            .bot-order-confirmation .text-sm { font-size: 15px; }
            .bot-order-confirmation .flex.justify-between { gap: 12px; flex-wrap: wrap; }
        }
    </style>
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
                @if($orderGroup->isBkashPersonal())
                    @if($orderGroup->needsBkashPersonalSubmission())
                        <div class="mb-6 rounded-lg border border-pink-200 bg-pink-50 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold" style="color: #E2136E;">
                                    {{ $orderGroup->latestBkashPersonalPayment ? translate('Your bKash payment could not be verified') : translate('Complete your bKash payment') }}
                                </p>
                                <p class="text-sm text-gray-600">{{ translate('Send money via bKash and submit the transaction ID so we can verify your payment.') }}</p>
                            </div>
                            <a href="{{ url('/orders/bkash-payment/'.$orderGroup->code.'?source=landing') }}"
                                class="shrink-0 inline-flex items-center justify-center rounded-md text-white font-semibold px-5 h-10 text-sm" style="background-color: #E2136E;">
                                {{ translate('Submit bKash Payment') }}
                            </a>
                        </div>
                    @else
                        <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                            {{ $orderGroup->latestBkashPersonalPayment?->status === 'verified' ? translate('Your bKash payment has been verified.') : translate('Your bKash payment information has been received and is awaiting verification.') }}
                        </div>
                    @endif
                @endif
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
                                    <p class="font-medium">{{ formatPrice($item->total_price) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Order Total -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total Amount Paid</span>
                        <span class="text-2xl font-bold text-green-600">{{ formatPrice($orderGroup->transaction->total_amount ?? 0) }}</span>
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

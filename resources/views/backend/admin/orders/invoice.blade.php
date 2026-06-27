<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('INVOICE') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style type="text/css">
        * {
            box-sizing: border-box;
        }

        body {
            font-family: {{ $font_family ?? 'roboto' }};
            color: #1c1917;
            background: #ffffff;
            line-height: 1.35;
            margin: 0;
            padding: 14px 14px 56px;
            direction: {{ $direction ?? 'ltr' }};
            text-align: {{ $default_text_align ?? 'left' }};
        }

        pre,
        p {
            padding: 0;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            text-align: {{ $default_text_align ?? 'left' }};
        }

        .text-end {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .visibleMobile {
            display: none;
        }

        .border-black {
            border: 1px solid #e7e5e4;
        }

        .border-b {
            border-bottom: 1px solid #e7e5e4;
        }

        .border-r {
            border-right: 1px solid #e7e5e4;
        }

        .border-t {
            border-top: 1px solid #e7e5e4;
        }

        .font-mono {
            font-family: inherit;
        }

        .font-price {
            font-family: inherit;
            white-space: nowrap;
        }

        .currency-symbol-left {
            display: inline-block;
            margin-right: 4px;
        }

        .currency-symbol-right {
            display: inline-block;
            margin-left: 4px;
        }

        .text-default {
            text-align: {{ $default_text_align ?? 'left' }};
        }

        .text-reverse {
            text-align: {{ $reverse_text_align ?? 'right' }};
        }

        .invoice-product-thumb {
            width: 34px;
            height: 34px;
            object-fit: cover;
            border: 1px solid #d6d3d1;
            border-radius: 4px;
        }

        .invoice-product-name {
            padding-left: 7px;
            line-height: 1.25;
        }

        .invoice-items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #a8a29e;
            margin-bottom: 10px;
        }

        .invoice-items-table th,
        .invoice-items-table td {
            border: 1px solid #a8a29e !important;
        }

        .invoice-items-inner,
        .invoice-items-inner td {
            border: 0 !important;
        }

        .invoice-page-footer {
            position: fixed;
            left: 14px;
            right: 14px;
            bottom: 12px;
            padding-top: 7px;
            border-top: 1px solid #e7e5e4;
            text-align: center;
            background: #ffffff;
        }
    </style>
</head>

<body>
    @php
        $orderGroup = $order->orderGroup;
        $user = $order->user;
        $customerName = $orderGroup?->name ?: $user?->name;
        $customerName = filled($customerName) ? $customerName : translate('Walk In Customer');
        $customerPhone = $orderGroup?->phone ?: $user?->phone;
        $customerEmail = $orderGroup?->email ?: $user?->email;
        $billingAddress = $orderGroup?->shipping_address ?: $orderGroup?->billingAddress?->address;
        $transaction = $orderGroup?->transaction;
        $paymentMethodRaw = $transaction?->payment_method;
        $paymentMethodLabels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'cod' => 'Cash On Delivery',
            'cash_on_delivery' => 'Cash On Delivery',
            'card_on_delivery' => 'Card On Delivery',
            'bank_transfer' => 'Bank Transfer',
        ];
        $paymentMethod = filled($paymentMethodRaw)
            ? ($paymentMethodLabels[$paymentMethodRaw] ?? ucwords(str_replace('_', ' ', $paymentMethodRaw)))
            : 'N/A';
        $paymentStatusRaw = $transaction?->status ?: $order->payment_status;
        $paymentStatusLabels = [
            'paid' => 'Paid',
            'unpaid' => 'Due',
            'pending' => 'Pending',
            'partial' => 'Partially Paid',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
        ];
        $paymentStatus = filled($paymentStatusRaw)
            ? ($paymentStatusLabels[$paymentStatusRaw] ?? ucwords(str_replace('_', ' ', $paymentStatusRaw)))
            : 'N/A';
        $isCashOnDelivery = in_array($paymentMethodRaw, ['cod', 'cash_on_delivery'], true);
        $cashOnDeliveryValue = $isCashOnDelivery ? $order->total_amount : 0;
        $isExchangeInvoice = $order->is_exchange_order && isset($exchange);
        $effectiveTotal = $isExchangeInvoice
            ? max($exchange->price_difference, 0)
            : (float) $order->total_amount;
        $paidAmount = match ($paymentStatusRaw) {
            'paid' => $effectiveTotal,
            'unpaid', 'pending', 'failed', 'cancelled' => 0.0,
            default => null,
        };
        $dueAmount = match ($paymentStatusRaw) {
            'paid' => 0.0,
            'unpaid', 'pending', 'failed', 'cancelled' => $effectiveTotal,
            default => null,
        };
        $logoText = substr($systemName ?? 'EP', 0, 2);
        $currencySymbol = getSetting('currencySymbol') ?? '$';
        $currencySymbolAlignment = (int) (getSetting('currencySymbolAlignment') ?? 0);
        $formatSummaryPrice = function ($amount) use ($currencySymbol, $currencySymbolAlignment) {
            $formattedAmount = e(formatPrice($amount, false, false));
            $escapedSymbol = e($currencySymbol);

            return match ($currencySymbolAlignment) {
                1 => '<span class="currency-symbol-left">' .
                    $escapedSymbol .
                    '</span><span>' .
                    $formattedAmount .
                    '</span>',
                2 => '<span>' .
                    $formattedAmount .
                    '</span><span class="currency-symbol-right">' .
                    $escapedSymbol .
                    '</span>',
                3 => '<span class="currency-symbol-left">' .
                    $escapedSymbol .
                    '</span><span>' .
                    $formattedAmount .
                    '</span>',
                default => '<span>' .
                    $formattedAmount .
                    '</span><span class="currency-symbol-right">' .
                    $escapedSymbol .
                    '</span>',
            };
        };
        $logoHtml = '';
        if ($logo && file_exists($logo)) {
            //  $logoHtml = '<img src="'.$logo.'" alt="logo" style="width: 140px; height: 140px; object-fit: contain; filter: contrast(1.02);">';

            $logoHtml =
                '<img src="' .
                $logo .
                '" alt="logo"
    style="width: 82px; height: auto;">';
        } else {
            $logoHtml =
                '<div style="width: 82px; height: 82px; background: #1c1917; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><span style="color: #fafaf9; font-size: 24px; font-weight: 700; letter-spacing: 2px;">' .
                $logoText .
                '</span></div>';
        }
        $pdfProductImage = function ($variation, $product) {
            $imageId = $variation?->image ?: $product?->thumbnail_image;
            if (!$imageId) {
                return null;
            }

            $mediaFile = \App\Models\MediaFile::find($imageId);
            if ($mediaFile && $mediaFile->media_file) {
                $absolutePath = public_path($mediaFile->media_file);
                if (file_exists($absolutePath)) {
                    return \App\Services\OrderService::makePdfSafeImage($absolutePath, 'product_pdf_safe');
                }
            }

            return uploadedAsset($imageId);
        };
    @endphp

    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;" class="text-default">
                <h1
                    style="font-size: 38px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: -2px; line-height: 1; color: #1c1917;">
                    @if ($isExchangeInvoice)
                        EXCHANGE
                    @else
                        INVOICE
                    @endif</h1>
                <div style="width: 52px; height: 3px; background: #1c1917; margin-top: 8px;"></div>
                <div style="margin-top: 8px; font-size: 14px; line-height: 1.45;">
                    <p style="margin-bottom: 4px;"><strong
                            style="color: #44403c; font-weight: 600;">{{ translate('Invoice Number') }}:</strong> <span
                            class="font-price">{{ getSetting('order_code_prefix') }}{{ $order->order_code }}</span>
                    </p>
                    <p><strong style="color: #44403c; font-weight: 600;">{{ translate('Invoice Date') }}:</strong>
                        {{ date('d M, Y', strtotime($order->created_at)) }}</p>
                </div>
            </td>
            <td style="vertical-align: top;" class="text-reverse">
                {!! $logoHtml !!}
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 16px;">
        <tr>
            <td style="vertical-align: top; width: 50%;" class="text-default">
                <h2
                    style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 1.5px; color: #78716c;">
                    {{ translate('From') }}</h2>
                <p style="font-weight: 700; font-size: 15px; color: #1c1917; margin-bottom: 5px;">
                    {{ $systemName ?? 'BUSINESS NAME' }}</p>
                @if (!empty($systemAddress))
                    <p style="font-size: 13px; white-space: pre-line; color: #57534e; line-height: 1.35;">
                        {{ $systemAddress }}</p>
                @endif
            </td>
            <td style="vertical-align: top; width: 50%;" class="text-reverse">
                <h2
                    style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 1.5px; color: #78716c;">
                    {{ translate('Bill To') }}</h2>
                <p style="font-weight: 700; font-size: 15px; color: #1c1917; margin-bottom: 5px;">
                    {{ $customerName }}</p>
                @if (filled($billingAddress))
                    <p
                        style="font-size: 13px; color: #57534e; white-space: pre-line; line-height: 1.35; margin-bottom: 3px;">
                        {{ $billingAddress }}</p>
                @endif
                @if (filled($customerPhone))
                    <p style="font-size: 13px; color: #57534e; margin-bottom: 3px;">
                        {{ translate('Phone') }}: {{ $customerPhone }}</p>
                @endif
                @if (filled($customerEmail))
                    <p style="font-size: 13px; color: #57534e; margin-bottom: 0;">
                        {{ translate('Email') }}: {{ $customerEmail }}</p>
                @endif
            </td>
        </tr>
    </table>

                @if ($isExchangeInvoice)
    <table class="invoice-items-table"
        style="margin-bottom: 8px; margin-top: 16px; border-radius: 4px; overflow: hidden;">
        <thead>
            <tr style="background: #fef3c7; border-bottom: 2px solid #d6d3d1;">
                <th colspan="4"
                    style="font-weight: 700; font-size: 13px; padding: 7px 12px; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; color: #92400e;">
                    {{ translate('Returned Items') }} ({{ translate('Credit') }})</th>
            </tr>
            <tr style="background: #f5f5f4; border-bottom: 2px solid #d6d3d1;">
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: left; border-right: 1px solid #e7e5e4; width: auto; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Item & Description') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: right; border-right: 1px solid #e7e5e4; width: 100px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Unit Price') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: center; border-right: 1px solid #e7e5e4; width: 60px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Qty') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: right; width: 120px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($exchange->items as $item)
                @php
                    $originalProduct = $item->originalVariation?->product;
                    $originalVariationLabel = $item->originalVariation?->combinations?->map(fn($c) => $c->variationValue?->name)?->filter()?->implode(' / ') ?? '';
                    $originalProductImage = $pdfProductImage($item->originalVariation, $originalProduct);
                @endphp
                <tr style="border-bottom: 1px solid #e7e5e4;">
                    <td
                        style="text-align: left; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917; vertical-align: top;">
                        <table class="invoice-items-inner" style="width: 100%; border-collapse: collapse;">
                            <tr>
                                @if ($originalProductImage)
                                    <td style="width: 40px; vertical-align: top;">
                                        <img src="{{ $originalProductImage }}" alt="" class="invoice-product-thumb">
                                    </td>
                                @endif
                                <td class="invoice-product-name" style="vertical-align: top; {{ $originalProductImage ? '' : 'padding-left: 0;' }}">
                                    {{ $originalProduct?->CollectTranslation('name') ?? translate('Product not found') }}
                                    @if ($originalVariationLabel)
                                        <br><span style="font-size: 12px; color: #78716c;">{{ $originalVariationLabel }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: right; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917;"
                        class="font-price">
                        {{ formatPrice($item->original_unit_price) }}</td>
                    <td style="text-align: center; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917;"
                        class="font-price">
                        {{ $item->original_quantity }}</td>
                    <td style="text-align: right; padding: 7px 12px; font-size: 14px; color: #15803d; font-weight: 500;"
                        class="font-price">
                        -{{ formatPrice($item->original_unit_price * $item->original_quantity) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="invoice-items-table"
        style="border-radius: 4px; overflow: hidden;">
        <thead>
            <tr style="background: #dbeafe; border-bottom: 2px solid #d6d3d1;">
                <th colspan="4"
                    style="font-weight: 700; font-size: 13px; padding: 7px 12px; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; color: #1e40af;">
                    {{ translate('Exchange Items') }} ({{ translate('New Items Received') }})</th>
            </tr>
            <tr style="background: #f5f5f4; border-bottom: 2px solid #d6d3d1;">
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: left; border-right: 1px solid #e7e5e4; width: auto; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Item & Description') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: right; border-right: 1px solid #e7e5e4; width: 100px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Unit Price') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: center; border-right: 1px solid #e7e5e4; width: 60px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Qty') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: right; width: 120px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($exchange->items as $item)
                @php
                    $exchangeProduct = $item->exchangeVariation?->product;
                    $exchangeVariationLabel = $item->exchangeVariation?->combinations?->map(fn($c) => $c->variationValue?->name)?->filter()?->implode(' / ') ?? '';
                    $exchangeProductImage = $pdfProductImage($item->exchangeVariation, $exchangeProduct);
                @endphp
                <tr style="border-bottom: 1px solid #e7e5e4;">
                    <td
                        style="text-align: left; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917; vertical-align: top;">
                        <table class="invoice-items-inner" style="width: 100%; border-collapse: collapse;">
                            <tr>
                                @if ($exchangeProductImage)
                                    <td style="width: 40px; vertical-align: top;">
                                        <img src="{{ $exchangeProductImage }}" alt="" class="invoice-product-thumb">
                                    </td>
                                @endif
                                <td class="invoice-product-name" style="vertical-align: top; {{ $exchangeProductImage ? '' : 'padding-left: 0;' }}">
                                    {{ $exchangeProduct?->CollectTranslation('name') ?? translate('Product not found') }}
                                    @if ($exchangeVariationLabel)
                                        <br><span style="font-size: 12px; color: #78716c;">{{ $exchangeVariationLabel }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: right; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917;"
                        class="font-price">
                        {{ formatPrice($item->exchange_unit_price) }}</td>
                    <td style="text-align: center; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917;"
                        class="font-price">
                        {{ $item->quantity }}</td>
                    <td style="text-align: right; padding: 7px 12px; font-size: 14px; color: #1c1917; font-weight: 500;"
                        class="font-price">
                        {{ formatPrice($item->exchange_unit_price * $item->quantity) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <table class="invoice-items-table"
        style="margin-top: 16px; border-radius: 4px; overflow: hidden;">
        <thead>
            <tr style="background: #f5f5f4; border-bottom: 2px solid #d6d3d1;">
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: left; border-right: 1px solid #e7e5e4; width: auto; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Item & Description') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: right; border-right: 1px solid #e7e5e4; width: 100px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Unit Price') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: center; border-right: 1px solid #e7e5e4; width: 60px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Qty') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 8px 12px; text-align: right; width: 120px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $key => $item)
                @php
                    $product = $item->productVariation->product ?? null;
                    $productImage = $pdfProductImage($item->productVariation, $product);
                @endphp
                <tr style="border-bottom: 1px solid #e7e5e4;">
                    <td
                        style="text-align: left; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917; vertical-align: top;">
                        <table class="invoice-items-inner" style="width: 100%; border-collapse: collapse;">
                            <tr>
                                @if ($productImage)
                                    <td style="width: 40px; vertical-align: top;">
                                        <img src="{{ $productImage }}" alt="" class="invoice-product-thumb">
                                    </td>
                                @endif
                                <td class="invoice-product-name" style="vertical-align: top; {{ $productImage ? '' : 'padding-left: 0;' }}">
                                    {{ $product?->CollectTranslation('name') ?? translate('Product not found') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: right; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917;"
                        class="font-price">
                        {{ formatPrice($item->unit_price) }}</td>
                    <td style="text-align: center; padding: 7px 12px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917;"
                        class="font-price">
                        {{ $item->qty }}</td>
                    <td style="text-align: right; padding: 7px 12px; font-size: 14px; color: #1c1917; font-weight: 500;"
                        class="font-price">
                        {{ formatPrice($item->total_price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="vertical-align: top; padding-right: 20px;">
                @if (isset($qrCodeImage))
                    <table style="width: 130px; background: #fafaf9; border-radius: 8px; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; text-align: center;">
                                <p
                                    style="font-size: 10px; color: #78716c; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; text-align: center;">
                                    {{ translate('Scan to verify invoice') }}
                                    <br>
                                </p>
                                <img src="{{ $qrCodeImage }}" alt="Invoice QR Code"
                                    style="width: 92px; height: 92px; display: block; margin: 0 auto;">
                                <p
                                    style="font-size: 9px; color: #a8a29e; margin: 5px 0 0 0; white-space: nowrap; text-align: center;">
                                    <br> {{ translate('View products online') }}
                                </p>
                            </td>
                        </tr>
                    </table>
                @endif
            </td>
            <td style="width: 420px; vertical-align: top;">
    @if ($isExchangeInvoice)
                <table
                    style="width: 100%; border: 1.5px solid #d6d3d1; background: #fff; border-radius: 4px; overflow: hidden; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Returned Items Total') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #15803d; width: 50%;"
                            class="font-price">
                            -{!! $formatSummaryPrice($exchange->total_original_price) !!}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Exchange Items Total') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #1c1917; width: 50%;"
                            class="font-price">
                            {!! $formatSummaryPrice($exchange->total_exchange_price) !!}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Shipping Cost') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #1c1917; width: 50%;"
                            class="font-price">
                            @if ($exchange->shipping_charge <= 0)
                                {{ translate('Free') }}
                            @else
                                {!! $formatSummaryPrice($exchange->shipping_charge) !!}
                            @endif
                        </td>
                    </tr>
                    <tr style="background: #f5f5f4;">
                        <td
                            style="text-align: left; padding: 10px 12px; font-weight: 700; font-size: 17px; color: #1c1917; text-transform: uppercase; letter-spacing: 0.5px; width: 50%;">
                            {{ translate('Price Difference') }}</td>
                        <td style="text-align: right; padding: 10px 12px; font-weight: 700; font-size: 17px; width: 50%;"
                            class="font-price">
                            @if ($exchange->price_difference > 0)
                                <span style="color: #c2410c;">+{!! $formatSummaryPrice($exchange->price_difference) !!}</span>
                            @elseif ($exchange->price_difference < 0)
                                <span style="color: #15803d;">{!! $formatSummaryPrice($exchange->price_difference) !!}</span>
                            @else
                                {!! $formatSummaryPrice(0) !!}
                            @endif
                        </td>
                    </tr>
                    @if ($exchange->price_difference > 0)
                    <tr style="border-top: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 6px 12px; font-size: 13px; color: #c2410c; width: 50%; font-style: italic;">
                            {{ translate('Customer Pays') }}</td>
                        <td style="text-align: right; padding: 6px 12px; font-size: 13px; color: #c2410c; width: 50%; font-style: italic;">
                            {{ translate('Amount Due') }}</td>
                    </tr>
                    @elseif ($exchange->price_difference < 0)
                    <tr style="border-top: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 6px 12px; font-size: 13px; color: #15803d; width: 50%; font-style: italic;">
                            {{ translate('Refund to Customer') }}</td>
                        <td style="text-align: right; padding: 6px 12px; font-size: 13px; color: #15803d; width: 50%; font-style: italic;">
                            {!! $formatSummaryPrice(abs($exchange->price_difference)) !!}</td>
                    </tr>
                    @else
                    <tr style="border-top: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 6px 12px; font-size: 13px; color: #57534e; width: 50%; font-style: italic;"
                            colspan="2">
                            {{ translate('Even Exchange - No Payment Required') }}</td>
                    </tr>
                    @endif
                    @if ($exchange->refund_amount)
                    <tr style="border-top: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Refund Amount') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #15803d; font-weight: 600; width: 50%;"
                            class="font-price">
                            {!! $formatSummaryPrice($exchange->refund_amount) !!}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Paid') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #15803d; width: 50%;"
                            class="font-price">
                            @if (!is_null($paidAmount))
                                {!! $formatSummaryPrice($paidAmount) !!}
                            @else
                                {{ $paymentStatus }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Due') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #b91c1c; width: 50%;"
                            class="font-price">
                            @if (!is_null($dueAmount))
                                {!! $formatSummaryPrice($dueAmount) !!}
                            @else
                                {{ translate('Pending') }}
                            @endif
                        </td>
                    </tr>
                </table>
                @else
                <table
                    style="width: 100%; border: 1.5px solid #d6d3d1; background: #fff; border-radius: 4px; overflow: hidden; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Sub Total') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #1c1917; width: 50%;"
                            class="font-price">
                            {!! $formatSummaryPrice($order->amount) !!}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Total Tax') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #1c1917; width: 50%;"
                            class="font-price">
                            {!! $formatSummaryPrice($order->tax_amount) !!}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Shipping Cost') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #1c1917; width: 50%;"
                            class="font-price">
                            @if ($order->shipping_charge_amount <= 0)
                                {{ translate('Free') }}
                            @else
                                {!! $formatSummaryPrice($order->shipping_charge_amount) !!}
                            @endif
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Discount') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #1c1917; width: 50%;"
                            class="font-price">
                            {!! $formatSummaryPrice($order->discount_amount + $order->coupon_discount_amount) !!}</td>
                    </tr>
                    <tr style="background: #f5f5f4;">
                        <td
                            style="text-align: left; padding: 10px 12px; font-weight: 700; font-size: 17px; color: #1c1917; text-transform: uppercase; letter-spacing: 0.5px; width: 50%;">
                            {{ translate('Grand Total') }}</td>
                        <td style="text-align: right; padding: 10px 12px; font-weight: 700; font-size: 17px; color: #1c1917; width: 50%;"
                            class="font-price">
                            {!! $formatSummaryPrice($order->total_amount) !!}</td>
                    </tr>
                    <tr style="border-top: 1px solid #e7e5e4;">
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Paid') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #15803d; width: 50%;"
                            class="font-price">
                            @if (!is_null($paidAmount))
                                {!! $formatSummaryPrice($paidAmount) !!}
                            @else
                                {{ $paymentStatus }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 7px 12px; font-size: 16px; color: #57534e; width: 50%;">
                            {{ translate('Due') }}</td>
                        <td style="text-align: right; padding: 7px 12px; font-size: 16px; color: #b91c1c; width: 50%;"
                            class="font-price">
                            @if (!is_null($dueAmount))
                                {!! $formatSummaryPrice($dueAmount) !!}
                            @else
                                {{ translate('Pending') }}
                            @endif
                        </td>
                    </tr>
                </table>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 12px;">
        <tr>
            <td style="vertical-align: top;" class="text-default">
                <h3
                    style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1.5px; color: #78716c;">
                    {{ translate('Payment Method') }}</h3>
                <p style="font-size: 15px; color: #1c1917; font-weight: 500;">
                    {{ $paymentMethod }}
                </p>
                <p style="font-size: 14px; color: #57534e; margin-top: 6px;">
                    {{ translate('Payment Status') }}: {{ $paymentStatus }}
                </p>
                @if ($isCashOnDelivery)
                    <p style="font-size: 14px; color: #57534e; margin-top: 6px;">
                        {{ translate('Cash on Delivery Value') }}:
                        <span class="font-price">{!! $formatSummaryPrice($cashOnDeliveryValue) !!}</span>
                    </p>
                @endif
            </td>
            <td style="vertical-align: top;" class="text-reverse">
                <h3
                    style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1.5px; color: #78716c;">
                    {{ translate('Prepared By') }}</h3>
                <p style="font-weight: 700; font-size: 15px; color: #1c1917; margin-bottom: 4px;">
                    {{ $systemName ?? 'Store' }}</p>
                <p style="font-size: 14px; color: #57534e;">{{ translate('Sales Department') }}</p>
            </td>
        </tr>
    </table>

    <div class="invoice-page-footer">
        <p style="font-size: 12px; color: #78716c; font-style: italic; letter-spacing: 0.3px;">
            {{ translate('Thanks for your Purchase') }}</p>
        <p style="font-size: 10px; color: #a8a29e; margin-top: 4px; letter-spacing: 0.5px;">
            {{ $systemName ?? 'Store' }} — {{ date('Y') }}</p>
    </div>
</body>

</html>

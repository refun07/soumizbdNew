<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Invoices') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style type="text/css">
        @page {
            sheet-size: A4;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: {{ $font_family ?? 'roboto' }};
            color: #172033;
            background: #ffffff;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            direction: {{ $direction ?? 'ltr' }};
            text-align: {{ $default_text_align ?? 'left' }};
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td,
        th {
            padding: 0;
            vertical-align: top;
        }

        .page-url {
            text-align: right;
            font-size: 12px;
            color: #475569;
            margin-bottom: 3px;
        }

        .invoice-box {
            border: 1px solid #cbd5e1;
            min-height: 0;
            padding: 7px 9px 8px;
            page-break-inside: auto;
            background: #ffffff;
        }

        .invoice-box + .invoice-box {
            margin-top: 5mm;
            border-top: 2px solid #64748b;
        }

        .section-title {
            font-weight: 700;
            font-size: 13.5px;
            margin-bottom: 3px;
            color: #475569;
            text-transform: uppercase;
        }

        .brand {
            text-align: center;
            font-weight: 800;
            font-size: 22px;
            color: #be123c;
            letter-spacing: .2px;
        }

        .brand-logo {
            max-width: 92px;
            max-height: 34px;
        }

        .shop-info {
            margin-top: 4px;
            text-align: center;
            font-size: 12.5px;
            line-height: 1.35;
            color: #334155;
            word-break: break-word;
        }

        .meta {
            text-align: right;
            font-size: 13.5px;
            line-height: 1.35;
            color: #172033;
            word-break: break-word;
        }

        .customer {
            font-size: 13.5px;
            line-height: 1.35;
            color: #172033;
            word-break: break-word;
        }

        .customer-name {
            font-weight: 700;
            font-size: 17px;
            color: #0f172a;
        }

        .items {
            margin-top: 6px;
            border: 1px solid #94a3b8;
            table-layout: fixed;
        }

        .items th {
            font-size: 13.5px;
            font-weight: 700;
            text-align: center;
            border-bottom: 1px solid #94a3b8;
            padding: 3px 4px;
            color: #0f172a;
            background: #f1f5f9;
        }

        .items td {
            font-size: 13.5px;
            padding: 3px 4px;
            border-top: 1px solid #e2e8f0;
        }

        .item-name {
            line-height: 1.25;
            color: #172033;
            word-break: break-word;
        }

        .product-thumb {
            width: 34px;
            height: 34px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
            margin-right: 5px;
        }

        .price,
        .qty {
            white-space: nowrap;
        }

        .price {
            text-align: right;
            font-family: hindsiliguri, {{ $font_family ?? 'roboto' }}, dejavusanscondensed;
            font-weight: bold !important;
            color: #000000;
        }

        .amount-value {
            font-family: hindsiliguri, {{ $font_family ?? 'roboto' }}, dejavusanscondensed;
            font-weight: bold !important;
            color: #000000;
        }

        .qty {
            text-align: center;
            font-family: hindsiliguri, {{ $font_family ?? 'roboto' }}, dejavusanscondensed;
            font-weight: bold !important;
            color: #000000;
        }

        .summary {
            width: 46%;
            margin-left: auto;
            margin-top: 4px;
            font-size: 13.5px;
        }

        .summary td {
            padding: 1.5px 0;
        }

        .summary .label {
            font-weight: 700;
            text-align: right;
            padding-right: 6px;
        }

        .summary .value {
            text-align: right;
            white-space: nowrap;
            font-weight: bold !important;
        }

        .footer-line {
            margin-top: 5px;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
            font-size: 12.5px;
            color: #475569;
        }
    </style>
</head>

<body>
    @php
        $host = request()->getSchemeAndHttpHost();
        $logoText = strtoupper(substr($systemName ?? 'SHOP', 0, 2));
        $paymentMethodLabels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'cod' => 'Cash On Delivery',
            'cash_on_delivery' => 'Cash On Delivery',
            'card_on_delivery' => 'Card On Delivery',
            'bank_transfer' => 'Bank Transfer',
        ];
        $uploadedImagePath = function ($fileId) {
            if (!$fileId) {
                return null;
            }

            $mediaFile = \App\Models\MediaFile::find($fileId);
            if ($mediaFile && $mediaFile->media_file) {
                $absolutePath = public_path($mediaFile->media_file);
                if (file_exists($absolutePath)) {
                    return $absolutePath;
                }
            }

            return uploadedAsset($fileId);
        };
    @endphp

    <div class="page-url">{{ $host }}</div>

    @foreach ($orders as $order)
                @php
                    $orderGroup = $order->orderGroup;
                    $user = $order->user;
                    $customerName = $orderGroup?->name ?: $user?->name;
                    $customerName = filled($customerName) ? $customerName : translate('Walk In Customer');
                    $customerPhone = $orderGroup?->phone ?: $user?->phone;
                    $billingAddress = $orderGroup?->shipping_address ?: $orderGroup?->billingAddress?->address;
                    $transaction = $orderGroup?->transaction;
                    $paymentMethodRaw = $transaction?->payment_method;
                    $paymentMethod = filled($paymentMethodRaw)
                        ? ($paymentMethodLabels[$paymentMethodRaw] ?? ucwords(str_replace('_', ' ', $paymentMethodRaw)))
                        : ucwords(str_replace('_', ' ', $order->payment_status));
                    $totalQty = $order->orderItems->sum('qty');
                    $codAmount = in_array($paymentMethodRaw, ['cod', 'cash_on_delivery'], true) ? $order->total_amount : 0;
                @endphp

                <div class="invoice-box">
                    <table class="invoice-header">
                        <tr>
                            <td style="width: 34%; padding-right: 8px;">
                                <div class="section-title">{{ translate('CUSTOMER Details') }}</div>
                                <div class="customer">
                                    <div class="customer-name">{{ $customerName }}</div>
                                    @if (filled($customerPhone))
                                        <div>{{ $customerPhone }}</div>
                                    @endif
                                    @if (filled($billingAddress))
                                        <div>{{ $billingAddress }}</div>
                                    @endif
                                    @if (filled($order->courier_name))
                                        <div>{{ ucfirst($order->courier_name) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 32%; text-align: center; padding: 0 8px;">
                                @if ($logo && file_exists($logo))
                                    <img src="{{ $logo }}" alt="logo" class="brand-logo">
                                @else
                                    <div class="brand">{{ $systemName ?? $logoText }}</div>
                                @endif
                                <div class="shop-info">
                                    <strong>{{ $systemName ?? 'NICE SHOP' }}</strong><br>
                                    @if (filled($systemAddress))
                                        {{ $systemAddress }}<br>
                                    @endif
                                    Visit us : or call {{ getSetting('systemPhone') ?? $customerPhone }}
                                </div>
                            </td>
                            <td style="width: 34%; padding-left: 8px;">
                                <div class="meta">
                                    <strong>Invoice #{{ getSetting('order_code_prefix') }}{{ $order->order_code }}</strong><br>
                                    <strong>Order Date :</strong> {{ date('Y-m-d', strtotime($order->created_at)) }}<br>
                                    <strong>Payment Method :</strong> {{ $paymentMethod }}<br>
                                    <strong>Cash On Delivery :</strong> <strong class="amount-value" style="font-weight: bold;">{{ formatPrice($codAmount) }}</strong><br>
                                    <strong>Customer Note:</strong>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <table class="items">
                        <thead>
                            <tr>
                                <th style="width: 64%;">Product</th>
                                <th style="width: 12%; text-align: center;">Quantity</th>
                                <th style="width: 24%; text-align: right;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $item)
                                @php
                                    $product = $item->productVariation?->product;
                                    $imageId = $item->productVariation?->image ?: $product?->thumbnail_image;
                                    $imageUrl = $uploadedImagePath($imageId);
                                @endphp
                                <tr>
                                    <td>
                                        <table style="table-layout: fixed;">
                                            <tr>
                                                @if ($imageUrl)
                                                    <td style="width: 42px;">
                                                        <img src="{{ $imageUrl }}" alt="" class="product-thumb">
                                                    </td>
                                                @endif
                                                <td class="item-name">
                                                    {{ $product?->CollectTranslation('name') ?? translate('Product not found') }}
                                                    @if ($item->productVariation?->code)
                                                        <br><span>Code: {{ generateVariationName($item->productVariation->code) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="qty" style="font-weight: bold;">{{ $item->qty }}</td>
                                    <td class="price amount-value" style="font-weight: bold;"><strong>{{ formatPrice($item->total_price) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="summary">
                        <tr>
                            <td class="label">Delivery :</td>
                            <td class="value amount-value" style="font-weight: bold;"><strong>{{ formatPrice($order->shipping_charge_amount) }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Total Due :</td>
                            <td class="value amount-value" style="font-weight: bold;"><strong>{{ formatPrice($order->total_amount) }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">Order Received By :</td>
                            <td class="value">{{ auth()->user()?->name ?? 'Admin' }}</td>
                        </tr>
                    </table>

                    <div class="footer-line">
                        Purchase date ({{ date('Y-m-d', strtotime($order->created_at)) }}). Total quantity: {{ $totalQty }}
                    </div>
                </div>
    @endforeach
</body>

</html>

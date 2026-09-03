<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Invoices') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    @php
        $halfPageItemLimit = 20;
    @endphp
    <style type="text/css">
        @page {
            sheet-size: A4;
            margin: 8mm 10mm 8mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: {{ $font_family ?? 'roboto' }};
            color: #0f172a;
            background: #ffffff;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            direction: {{ $direction ?? 'ltr' }};
            text-align: {{ $default_text_align ?? 'left' }};
            font-size: 11px;
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

        .invoice-box {
            border: 0;
            padding: 0;
            background: #ffffff;
        }

        .invoice-box + .invoice-box {
            page-break-before: always;
            border-top: 0;
        }

        .layout-half {
            max-height: 136mm;
            overflow: hidden;
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .invoice-header td {
            vertical-align: middle;
        }

        .company-logo-cell {
            width: 14%;
            padding-right: 6px;
        }

        .company-info-cell {
            width: 48%;
        }

        .invoice-meta-cell {
            width: 38%;
            text-align: right;
            vertical-align: top;
        }

        .brand-logo {
            max-width: 64px;
            max-height: 28px;
        }

        .brand {
            font-weight: 700;
            font-size: 16px;
            color: #d90429;
        }

        .shop-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.05;
            text-transform: uppercase;
        }

        .shop-line {
            font-size: 10px;
            line-height: 1.35;
            color: #0f172a;
            white-space: nowrap;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: 400;
            color: #000000;
            line-height: 1;
        }

        .invoice-meta {
            font-size: 10px;
            line-height: 1.4;
            color: #0f172a;
            margin-top: 5px;
        }

        .invoice-meta-line {
            white-space: nowrap !important;
        }

        .divider {
            border-top: 1px solid #cbd5e1;
            margin: 6px 0;
            height: 1px;
        }

        .section-title {
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 3px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .bill-table td {
            vertical-align: middle;
        }

        .customer {
            font-size: 11px;
            line-height: 1.35;
            color: #0f172a;
            word-break: break-word;
        }

        .customer-label {
            font-weight: 700;
        }

        .bill-rule {
            border-top: 1px solid #cbd5e1;
            width: 92%;
            margin: 3px 0 5px;
        }

        .qr-placeholder {
            width: 42px;
            height: 42px;
            border: 3px solid #111827;
            margin: 0 auto;
            text-align: center;
            font-size: 8px;
            line-height: 36px;
            color: #111827;
            font-weight: 800;
        }

        .qr-code {
            display: block;
            width: 42px;
            height: 42px;
            margin: 0 auto;
        }

        table.cod-box {
            width: 100%;
            border: 1px solid #0f172a;
            border-collapse: separate !important;
            border-spacing: 0;
            table-layout: auto;
            color: #0f172a;
        }

        table.cod-box td.cod-cell {
            padding: 12px 16px !important;
            border: 0 !important;
            text-align: center;
            vertical-align: middle !important;
        }

        .cod-label {
            color: #64748b;
            font-size: 9px;
            letter-spacing: .6px;
            text-transform: uppercase;
            line-height: 1.5;
            border: 0 !important;
        }

        .cod-amount {
            font-size: 16px;
            line-height: 1.5;
            font-weight: 800;
            letter-spacing: .2px;
            border: 0 !important;
        }

        .items {
            margin-top: 4px;
            border: 0;
            table-layout: fixed;
        }

        .items th {
            font-size: 10px;
            font-weight: 700;
            text-align: center;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 3px 3px;
            color: #0f172a;
            background: #f1f5f9;
        }

        .items td {
            font-size: 10px;
            padding: 3px 3px;
            border-bottom: 1px solid #cbd5e1;
        }

        .item-name {
            line-height: 1.2;
            color: #172033;
            word-break: break-word;
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
            width: 42%;
            margin-left: auto;
            margin-top: 4px;
            font-size: 10px;
        }

        .summary td {
            padding: 2px 0;
        }

        .summary .label {
            font-weight: 400;
            text-align: left;
            padding-left: 0;
        }

        .summary .value {
            text-align: right;
            white-space: nowrap;
            font-weight: bold !important;
        }

        .summary-total td {
            border-top: 1px solid #cbd5e1;
            font-weight: 800;
            padding-top: 4px;
        }

        .footer-line {
            margin-top: 6px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 8px;
            text-align: center;
            color: #64748b;
            letter-spacing: .6px;
        }

        /* 1-4 items: readable half-page */
        .density-comfortable .shop-title { font-size: 16px; }
        .density-comfortable .shop-line { font-size: 10px; }
        .density-comfortable .invoice-title { font-size: 26px; }
        .density-comfortable .invoice-meta { font-size: 10px; }
        .density-comfortable .section-title { font-size: 12px; }
        .density-comfortable .customer { font-size: 11px; }
        .density-comfortable .items th,
        .density-comfortable .items td { font-size: 10px; padding: 4px 3px; }
        .density-comfortable .summary { font-size: 10px; }
        .density-comfortable .qr-code,
        .density-comfortable .qr-placeholder { width: 44px; height: 44px; }
        .density-comfortable table.cod-box td.cod-cell { padding: 14px 16px !important; }
        .density-comfortable .cod-amount { font-size: 16px; }
        .density-comfortable .brand-logo { max-width: 64px; max-height: 28px; }

        /* 5-8 items */
        .density-medium .shop-title { font-size: 14px; }
        .density-medium .shop-line { font-size: 9px; }
        .density-medium .invoice-title { font-size: 22px; }
        .density-medium .invoice-meta { font-size: 9px; margin-top: 3px; }
        .density-medium .section-title { font-size: 11px; }
        .density-medium .customer { font-size: 10px; line-height: 1.28; }
        .density-medium .items th,
        .density-medium .items td { font-size: 9px; padding: 2.5px 3px; }
        .density-medium .summary { font-size: 9px; margin-top: 3px; }
        .density-medium .summary td { padding: 1.5px 0; }
        .density-medium .divider { margin: 5px 0; }
        .density-medium .qr-code,
        .density-medium .qr-placeholder { width: 38px; height: 38px; }
        .density-medium table.cod-box td.cod-cell { padding: 12px 14px !important; }
        .density-medium .cod-amount { font-size: 14px; }
        .density-medium .brand-logo { max-width: 56px; max-height: 24px; }

        /* 9-12 items */
        .density-compact .shop-title { font-size: 13px; }
        .density-compact .shop-line { font-size: 8.5px; }
        .density-compact .invoice-title { font-size: 18px; }
        .density-compact .invoice-meta { font-size: 8.5px; margin-top: 2px; }
        .density-compact .section-title { font-size: 10px; margin-bottom: 2px; }
        .density-compact .customer { font-size: 9px; line-height: 1.22; }
        .density-compact .items th,
        .density-compact .items td { font-size: 8.5px; padding: 1.5px 2px; }
        .density-compact .summary { font-size: 8.5px; margin-top: 2px; }
        .density-compact .summary td { padding: 1px 0; }
        .density-compact .divider { margin: 4px 0; }
        .density-compact .bill-rule { margin: 2px 0 3px; }
        .density-compact .qr-code,
        .density-compact .qr-placeholder { width: 32px; height: 32px; }
        .density-compact table.cod-box td.cod-cell { padding: 10px 12px !important; }
        .density-compact .cod-label { font-size: 8px; }
        .density-compact .cod-amount { font-size: 12px; }
        .density-compact .brand-logo { max-width: 48px; max-height: 22px; }
        .density-compact .footer-line { margin-top: 4px; padding-top: 3px; font-size: 7px; }

        /* 13-16 items */
        .density-tight .shop-title { font-size: 12px; }
        .density-tight .shop-line { font-size: 8px; }
        .density-tight .invoice-title { font-size: 16px; }
        .density-tight .invoice-meta { font-size: 8px; margin-top: 2px; }
        .density-tight .section-title { font-size: 9px; margin-bottom: 1px; }
        .density-tight .customer { font-size: 8px; line-height: 1.18; }
        .density-tight .items { margin-top: 2px; }
        .density-tight .items th,
        .density-tight .items td { font-size: 8px; padding: 1px 2px; }
        .density-tight .summary { font-size: 8px; margin-top: 2px; }
        .density-tight .summary td { padding: 0.5px 0; }
        .density-tight .summary-total td { padding-top: 2px; }
        .density-tight .divider { margin: 3px 0; }
        .density-tight .bill-rule { margin: 1px 0 2px; }
        .density-tight .qr-code,
        .density-tight .qr-placeholder { width: 26px; height: 26px; }
        .density-tight table.cod-box td.cod-cell { padding: 8px 10px !important; }
        .density-tight .cod-label { font-size: 7px; }
        .density-tight .cod-amount { font-size: 11px; }
        .density-tight .brand-logo { max-width: 42px; max-height: 18px; }
        .density-tight .footer-line { margin-top: 3px; padding-top: 2px; font-size: 7px; }
        .density-tight .company-logo-cell { width: 12%; padding-right: 4px; }

        /* 17-20 items: smallest still readable half-page */
        .density-dense .shop-title { font-size: 11px; }
        .density-dense .shop-line { font-size: 7.5px; }
        .density-dense .invoice-title { font-size: 14px; }
        .density-dense .invoice-meta { font-size: 7.5px; margin-top: 1px; }
        .density-dense .section-title { font-size: 8px; margin-bottom: 1px; }
        .density-dense .customer { font-size: 7.5px; line-height: 1.15; }
        .density-dense .items { margin-top: 1px; }
        .density-dense .items th,
        .density-dense .items td { font-size: 7px; padding: 0 2px; }
        .density-dense .item-name { line-height: 1.1; }
        .density-dense .summary { font-size: 7px; margin-top: 1px; }
        .density-dense .summary td { padding: 0; }
        .density-dense .summary-total td { padding-top: 1px; }
        .density-dense .divider { margin: 1.5px 0; }
        .density-dense .bill-rule { margin: 1px 0 1px; }
        .density-dense .qr-code,
        .density-dense .qr-placeholder { width: 20px; height: 20px; }
        .density-dense table.cod-box td.cod-cell { padding: 7px 9px !important; }
        .density-dense .cod-label { font-size: 6px; }
        .density-dense .cod-amount { font-size: 9px; }
        .density-dense .brand-logo { max-width: 36px; max-height: 16px; }
        .density-dense .footer-line { display: none; }
        .density-dense .company-logo-cell { width: 11%; padding-right: 3px; }

        /* 21+ items: full A4, may flow to extra pages */
        .density-full .layout-half { max-height: none; overflow: visible; }
        .density-full .shop-title { font-size: 18px; }
        .density-full .shop-line { font-size: 11px; }
        .density-full .invoice-title { font-size: 28px; }
        .density-full .invoice-meta { font-size: 11px; }
        .density-full .section-title { font-size: 13px; }
        .density-full .customer { font-size: 12px; line-height: 1.4; }
        .density-full .items th,
        .density-full .items td { font-size: 11px; padding: 5px 4px; }
        .density-full .summary { font-size: 11px; margin-top: 8px; }
        .density-full .summary td { padding: 3px 0; }
        .density-full .divider { margin: 8px 0; }
        .density-full .qr-code,
        .density-full .qr-placeholder { width: 52px; height: 52px; }
        .density-full table.cod-box td.cod-cell { padding: 14px 16px !important; }
        .density-full .cod-amount { font-size: 18px; }
        .density-full .brand-logo { max-width: 72px; max-height: 32px; }
        .density-full .footer-line { margin-top: 10px; padding-top: 6px; font-size: 9px; }
    </style>
</head>

<body>
    @php
        $paymentMethodLabels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'cod' => 'Cash On Delivery',
            'cash_on_delivery' => 'Cash On Delivery',
            'card_on_delivery' => 'Card On Delivery',
            'bank_transfer' => 'Bank Transfer',
        ];
        $formatInvoicePrice = function ($amount) {
            $currencySymbol = (string) (getSetting('currencySymbol') ?? '$');
            $currencyCode = strtoupper((string) (getSetting('currencyCode') ?? ''));

            // The bundled PDF fonts do not contain the Bengali taka glyph.
            return $currencyCode === 'BDT' || bin2hex($currencySymbol) === 'e09fb3'
                ? 'BDT '.formatPrice($amount, false, false)
                : formatPrice($amount);
        };
        $invoiceDensityForCount = function (int $itemCount) use ($halfPageItemLimit): string {
            if ($itemCount > $halfPageItemLimit) {
                return 'full';
            }
            if ($itemCount <= 4) {
                return 'comfortable';
            }
            if ($itemCount <= 8) {
                return 'medium';
            }
            if ($itemCount <= 12) {
                return 'compact';
            }
            if ($itemCount <= 16) {
                return 'tight';
            }

            return 'dense';
        };
        $shopPhone = getSetting('systemPhone') ?: getSetting('phone') ?: '01765765647';
        $shopAddress = $systemAddress ?: 'Elephant Road, Dhaka';
        $logoText = strtoupper(substr($systemName ?? 'SHOP', 0, 2));
        $formatInvoiceNumber = function ($code): string {
            $raw = ltrim((string) $code, '# ');
            if ($raw === '') {
                return 'RM';
            }

            return str_starts_with(strtoupper($raw), 'RM') ? $raw : 'RM'.$raw;
        };
    @endphp

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
                    $automaticItemDiscount = $order->orderItems->sum(fn($item) => (float) ($item->total_discount ?? 0));
                    $displaySubtotal = $order->orderItems->sum(function ($item) {
                        return max((float) $item->total_price - (float) ($item->total_tax ?? 0), 0);
                    });
                    $baseSubtotal = $order->orderItems->sum(function ($item) {
                        return (float) $item->unit_price * max((int) $item->qty, 1);
                    });
                    $storesBaseSubtotal = abs((float) $order->amount - $baseSubtotal) < 0.01;
                    $manualDiscount = $storesBaseSubtotal
                        ? max((float) $order->discount_amount - $automaticItemDiscount, 0)
                        : max((float) $order->discount_amount, 0);
                    $displayDiscount = $manualDiscount + (float) ($order->coupon_discount_amount ?? 0);
                    $displayTotal = max($displaySubtotal + (float) $order->tax_amount + (float) $order->shipping_charge_amount - $displayDiscount, 0);
                    $codAmount = in_array($paymentMethodRaw, ['cod', 'cash_on_delivery'], true) ? $displayTotal : 0;
                    $itemCount = $order->orderItems->count();
                    $isHalfPage = $itemCount <= $halfPageItemLimit;
                    $density = $invoiceDensityForCount($itemCount);
                    $invoiceCode = $formatInvoiceNumber(getSetting('order_code_prefix').($order->orderGroup?->code ?? $order->order_code));
                @endphp

                <div class="invoice-box layout-{{ $isHalfPage ? 'half' : 'full' }} density-{{ $density }}">
                    <table class="invoice-header">
                        <tr>
                            <td class="company-logo-cell">
                                @if ($logo && file_exists($logo))
                                    <img src="{{ $logo }}" alt="logo" class="brand-logo">
                                @else
                                    <div class="brand">{{ $systemName ?? $logoText }}</div>
                                @endif
                            </td>
                            <td class="company-info-cell">
                                <div class="shop-title">{{ $systemName ?? 'RAISA MART' }}</div>
                                <div class="shop-line">Hotline: {{ $shopPhone }}</div>
                                @if (filled($shopAddress))
                                <div class="shop-line">{{ $shopAddress }}</div>
                                @endif
                            </td>
                            <td class="invoice-meta-cell">
                                <div class="invoice-title">Invoice</div>
                                <div class="invoice-meta">
                                    <div class="invoice-meta-line">Invoice No : {{ $invoiceCode }}</div>
                                    <div class="invoice-meta-line">Date : {{ date('d-m-Y', strtotime($order->created_at)) }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="divider"></div>

                    <table class="bill-table">
                        <tr>
                            <td style="width: 45%; padding-right: 7px;">
                                <div class="section-title">Bill To</div>
                                <div class="bill-rule"></div>
                                <div class="customer">
                                    <div><span class="customer-label">Name :</span> {{ $customerName }}</div>
                                    @if (filled($customerPhone))
                                        <div><span class="customer-label">Phone :</span> {{ $customerPhone }}</div>
                                    @endif
                                    @if (filled($billingAddress))
                                        <div><span class="customer-label">Address :</span> {{ $billingAddress }}</div>
                                    @endif
                                    @if (filled($order->courier_name))
                                        <div><span class="customer-label">Courier :</span> {{ ucfirst($order->courier_name) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td style="width: 16%; text-align: center;">
                                @if (filled($qrCodeImage ?? null))
                                    <img src="{{ $qrCodeImage }}" alt="Order QR code" class="qr-code">
                                @else
                                    <div class="qr-placeholder">QR</div>
                                @endif
                            </td>
                            <td style="width: 39%; padding-left: 12px;">
                                <table class="cod-box" width="100%" cellpadding="12" cellspacing="0">
                                    <tr>
                                        <td class="cod-cell">
                                            <span class="cod-label">{{ $paymentMethod }}</span>
                                            <br><br>
                                            <span class="cod-amount">{{ $codAmount > 0 ? 'COD' : 'TOTAL' }} : {{ formatPrice($codAmount > 0 ? $codAmount : $displayTotal, false, false) }} BDT</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="divider"></div>

                    <div class="section-title" style="text-transform: none;">Product Details</div>
                    <table class="items">
                        <thead>
                            <tr>
                                <th style="width: 8%;">S.No</th>
                                <th style="width: 38%; text-align: left;">Product</th>
                                <th style="width: 14%;">Size</th>
                                <th style="width: 12%;">Quantity</th>
                                <th style="width: 14%; text-align: right;">Unit price</th>
                                <th style="width: 14%; text-align: right;">Total price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderItems as $itemIndex => $item)
                                @php
                                    $product = $item->productVariation?->product;
                                    $variationLabel = $item->productVariation?->combinations?->map(fn($combination) => $combination->variationValue?->name)->filter()->implode(' / ');
                                    $itemQuantity = max((int) $item->qty, 1);
                                    $discountedLineSubtotal = max((float) $item->total_price - (float) ($item->total_tax ?? 0), 0);
                                    $displayUnitPrice = $discountedLineSubtotal / $itemQuantity;
                                @endphp
                                <tr>
                                    <td class="qty">{{ $itemIndex + 1 }}</td>
                                    <td class="item-name">
                                        {{ $product?->CollectTranslation('name') ?? translate('Product not found') }}
                                        @if (! $isHalfPage && $item->productVariation?->code)
                                            <br><span>Code: {{ generateVariationName($item->productVariation->code) }}</span>
                                        @endif
                                    </td>
                                    <td class="qty">{{ $variationLabel ?: '-' }}</td>
                                    <td class="qty">{{ $item->qty }}</td>
                                    <td class="price amount-value" style="font-weight: bold;"><strong>{{ $formatInvoicePrice($displayUnitPrice) }}</strong></td>
                                    <td class="price amount-value" style="font-weight: bold;"><strong>{{ $formatInvoicePrice($item->total_price) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="summary">
                        <tr>
                            <td class="label">Subtotal :</td>
                            <td class="value amount-value" style="font-weight: bold;"><strong>{{ $formatInvoicePrice($displaySubtotal) }}</strong></td>
                        </tr>
                        @if ((float) $order->tax_amount > 0)
                            <tr>
                                <td class="label">Tax :</td>
                                <td class="value amount-value" style="font-weight: bold;"><strong>{{ $formatInvoicePrice($order->tax_amount) }}</strong></td>
                            </tr>
                        @endif
                        @if ($displayDiscount > 0)
                            <tr>
                                <td class="label">Discount :</td>
                                <td class="value amount-value" style="font-weight: bold; color: #b91c1c;"><strong>-{{ $formatInvoicePrice($displayDiscount) }}</strong></td>
                            </tr>
                        @endif
                        @if ((float) $order->shipping_charge_amount > 0)
                            <tr>
                                <td class="label">Shipping</td>
                                <td class="value amount-value" style="font-weight: bold;"><strong>{{ $formatInvoicePrice($order->shipping_charge_amount) }}</strong></td>
                            </tr>
                        @endif
                        <tr class="summary-total">
                            <td class="label">Total</td>
                            <td class="value amount-value" style="font-weight: bold;"><strong>{{ $formatInvoicePrice($displayTotal) }}</strong></td>
                        </tr>
                    </table>

                    <div class="footer-line">
                        Thank you for shopping with {{ $systemName ?? 'RAISA MART' }}
                    </div>
                </div>
    @endforeach
</body>

</html>

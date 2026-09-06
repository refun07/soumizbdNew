<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Invoices') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    @php
        $halfPageItemLimit = 10;
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
            font-size: 12px;
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
            font-size: 13px;
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
            padding: 3px 1px;
            color: #0f172a;
            background: #f1f5f9;
            white-space: nowrap !important;
            word-break: keep-all;
            overflow-wrap: normal;
            hyphens: none;
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
            white-space: nowrap !important;
        }

        .price {
            text-align: right;
            font-family: hindsiliguri, {{ $font_family ?? 'roboto' }}, dejavusanscondensed;
            font-weight: bold !important;
            color: #000000;
            white-space: nowrap !important;
        }

        .amount-inline {
            white-space: nowrap !important;
        }

        .items td.price {
            white-space: nowrap !important;
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

        /*
         * Half-page invoices (1-10 items) target ~136mm.
         * Product list is the primary font/padding knob.
         * Header, bill-to, COD, and summary only shrink from 6 items up.
         */

        /* 1-5 items: shared large chrome so 2-5 only need to shrink the product list */
        .density-spacious .shop-title,
        .density-few .shop-title,
        .density-comfortable .shop-title { font-size: 18px; }
        .density-spacious .shop-line,
        .density-few .shop-line,
        .density-comfortable .shop-line { font-size: 13px; }
        .density-spacious .invoice-title,
        .density-few .invoice-title,
        .density-comfortable .invoice-title { font-size: 30px; }
        .density-spacious .invoice-meta,
        .density-few .invoice-meta,
        .density-comfortable .invoice-meta { font-size: 12px; margin-top: 6px; }
        .density-spacious .section-title,
        .density-few .section-title,
        .density-comfortable .section-title { font-size: 14px; }
        .density-spacious .customer,
        .density-few .customer,
        .density-comfortable .customer { font-size: 15px; line-height: 1.4; }
        .density-spacious .summary,
        .density-few .summary,
        .density-comfortable .summary { font-size: 13px; margin-top: 8px; }
        .density-spacious .summary td,
        .density-few .summary td,
        .density-comfortable .summary td { padding: 3px 0; }
        .density-spacious .divider,
        .density-few .divider,
        .density-comfortable .divider { margin: 8px 0; }
        .density-spacious .qr-code,
        .density-spacious .qr-placeholder,
        .density-few .qr-code,
        .density-few .qr-placeholder,
        .density-comfortable .qr-code,
        .density-comfortable .qr-placeholder { width: 52px; height: 52px; }
        .density-spacious table.cod-box td.cod-cell,
        .density-few table.cod-box td.cod-cell,
        .density-comfortable table.cod-box td.cod-cell { padding: 16px 18px !important; }
        .density-spacious .cod-amount,
        .density-few .cod-amount,
        .density-comfortable .cod-amount { font-size: 20px; }
        .density-spacious .brand-logo,
        .density-few .brand-logo,
        .density-comfortable .brand-logo { max-width: 72px; max-height: 32px; }
        .density-spacious .footer-line,
        .density-few .footer-line,
        .density-comfortable .footer-line { margin-top: 8px; padding-top: 5px; font-size: 9px; }

        /* 1 product: enlarge every font so the invoice occupies near half-page */
        .density-spacious .shop-title { font-size: 20px; }
        .density-spacious .shop-line { font-size: 14px; }
        .density-spacious .invoice-title { font-size: 32px; }
        .density-spacious .invoice-meta { font-size: 12px; margin-top: 6px; }
        .density-spacious .section-title { font-size: 15px; }
        .density-spacious .customer { font-size: 16px; line-height: 1.4; }
        .density-spacious .items { margin-top: 6px; }
        .density-spacious .items th,
        .density-spacious .items td { font-size: 16px; padding: 5mm 1mm; }
        .density-spacious .items td.price { padding-left: 1mm; padding-right: 1mm; }
        .density-spacious .item-name { line-height: 1.4; }
        .density-spacious .summary { font-size: 13px; margin-top: 8px; }
        .density-spacious .qr-code,
        .density-spacious .qr-placeholder { width: 56px; height: 56px; }
        .density-spacious table.cod-box td.cod-cell { padding: 16px 18px !important; }
        .density-spacious .cod-amount { font-size: 22px; }
        .density-spacious .brand-logo { max-width: 80px; max-height: 36px; }
        .density-spacious .divider { margin: 8px 0; }
        .density-spacious .footer-line { margin-top: 8px; padding-top: 5px; font-size: 10px; }

        /* 2-3 items: keep chrome, shrink product list only */
        .density-few .items { margin-top: 5px; }
        .density-few .items th,
        .density-few .items td { font-size: 14px; padding: 2.2mm 1.5mm; }
        .density-few .item-name { line-height: 1.3; }

        /* 4-5 items: keep chrome, shrink product list further */
        .density-comfortable .items { margin-top: 3px; }
        .density-comfortable .items th,
        .density-comfortable .items td { font-size: 12px; padding: 1.1mm 1mm; }
        .density-comfortable .item-name { line-height: 1.25; }

        /* 6-8 items: shrink product list more, ease other fonts a little */
        .density-medium .shop-title { font-size: 17px; }
        .density-medium .shop-line { font-size: 13px; }
        .density-medium .invoice-title { font-size: 28px; }
        .density-medium .invoice-meta { font-size: 11px; margin-top: 4px; }
        .density-medium .section-title { font-size: 13px; }
        .density-medium .customer { font-size: 14px; line-height: 1.32; }
        .density-medium .items { margin-top: 3px; }
        .density-medium .items th,
        .density-medium .items td { font-size: 11px; padding: 1mm 0.8mm; }
        .density-medium .summary { font-size: 11px; margin-top: 5px; }
        .density-medium .summary td { padding: 2px 0; }
        .density-medium .divider { margin: 6px 0; }
        .density-medium .qr-code,
        .density-medium .qr-placeholder { width: 46px; height: 46px; }
        .density-medium table.cod-box td.cod-cell { padding: 13px 15px !important; }
        .density-medium .cod-amount { font-size: 18px; }
        .density-medium .brand-logo { max-width: 64px; max-height: 28px; }
        .density-medium .footer-line { margin-top: 6px; padding-top: 4px; font-size: 8px; }

        /* 9-10 items: tightest half-page */
        .density-compact .shop-title { font-size: 16px; }
        .density-compact .shop-line { font-size: 12px; }
        .density-compact .invoice-title { font-size: 24px; }
        .density-compact .invoice-meta { font-size: 10px; margin-top: 3px; }
        .density-compact .section-title { font-size: 12px; margin-bottom: 2px; }
        .density-compact .customer { font-size: 13px; line-height: 1.28; }
        .density-compact .items { margin-top: 2px; }
        .density-compact .items th,
        .density-compact .items td { font-size: 10px; padding: 1.1mm 0.8mm; }
        .density-compact .summary { font-size: 10px; margin-top: 3px; }
        .density-compact .summary td { padding: 1.5px 0; }
        .density-compact .divider { margin: 5px 0; }
        .density-compact .bill-rule { margin: 2px 0 4px; }
        .density-compact .qr-code,
        .density-compact .qr-placeholder { width: 42px; height: 42px; }
        .density-compact table.cod-box td.cod-cell { padding: 12px 14px !important; }
        .density-compact .cod-label { font-size: 8px; }
        .density-compact .cod-amount { font-size: 16px; }
        .density-compact .brand-logo { max-width: 56px; max-height: 24px; }
        .density-compact .footer-line { margin-top: 5px; padding-top: 3px; font-size: 8px; }

        /* 11+ items: full A4, may flow to extra pages */
        .density-full .layout-half { max-height: none; overflow: visible; }
        .density-full .shop-title { font-size: 18px; }
        .density-full .shop-line { font-size: 13px; }
        .density-full .invoice-title { font-size: 28px; }
        .density-full .invoice-meta { font-size: 11px; }
        .density-full .section-title { font-size: 13px; }
        .density-full .customer { font-size: 14px; line-height: 1.4; }
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
            // NBSP keeps "BDT" glued to the amount so mPDF cannot wrap it onto its own line.
            return $currencyCode === 'BDT' || bin2hex($currencySymbol) === 'e09fb3'
                ? 'BDT'."\u{00A0}".formatPrice($amount, false, false)
                : formatPrice($amount);
        };
        $invoiceDensityForCount = function (int $itemCount) use ($halfPageItemLimit): string {
            if ($itemCount > $halfPageItemLimit) {
                return 'full';
            }
            if ($itemCount <= 1) {
                return 'spacious';
            }
            if ($itemCount <= 3) {
                return 'few';
            }
            if ($itemCount <= 5) {
                return 'comfortable';
            }
            if ($itemCount <= 8) {
                return 'medium';
            }

            return 'compact';
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
                                <th nowrap style="width: 8%;">S.No</th>
                                <th style="width: 30%; text-align: left;">Product</th>
                                <th nowrap style="width: 10%;">Size</th>
                                <th nowrap style="width: 14%;">Quantity</th>
                                <th nowrap style="width: 19%; text-align: right;">Unit price</th>
                                <th nowrap style="width: 19%; text-align: right;">Total price</th>
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
                                    <td class="price amount-value" nowrap style="font-weight: bold; white-space: nowrap;"><strong class="amount-inline">{{ $formatInvoicePrice($displayUnitPrice) }}</strong></td>
                                    <td class="price amount-value" nowrap style="font-weight: bold; white-space: nowrap;"><strong class="amount-inline">{{ $formatInvoicePrice($item->total_price) }}</strong></td>
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

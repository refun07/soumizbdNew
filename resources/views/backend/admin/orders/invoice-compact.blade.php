<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Invoices') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    @php
        $isSingleInvoice = (bool) ($single ?? false);
    @endphp
    <style type="text/css">
        @page {
            sheet-size: A4;
            margin: {{ $isSingleInvoice ? '0' : '8mm' }};
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
            font-size: {{ $isSingleInvoice ? '8px' : '11px' }};
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
            min-height: 0;
            padding: 0;
            background: #ffffff;
        }

        .invoice-box + .invoice-box {
            page-break-before: always;
            border-top: 0;
        }

        .invoice-header td {
            vertical-align: middle;
        }

        .company-logo-cell {
            width: 14%;
            padding-right: 5px;
        }

        .company-info-cell {
            width: 44%;
        }

        .invoice-meta-cell {
            width: 42%;
            text-align: right;
            vertical-align: top;
        }

        .brand-logo {
            max-width: 76px;
            max-height: 34px;
        }

        .brand {
            font-weight: 700;
            font-size: 20px;
            color: #d90429;
        }

        .shop-title {
            font-size: 21px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.05;
            text-transform: uppercase;
        }

        .shop-line {
            font-size: 13px;
            line-height: 1.45;
            color: #0f172a;
            white-space: nowrap;
        }

        .shop-accent {
            color: #d90429;
            font-weight: 700;
            display: inline-block;
            min-width: 13px;
        }

        .contact-icon {
            display: inline-block !important;
            width: 6px !important;
            height: 6px !important;
            min-width: 6px !important;
            max-width: 6px !important;
            min-height: 6px !important;
            max-height: 6px !important;
            margin-right: 2px;
            vertical-align: 0;
            color: #d90429;
        }

        .contact-icon svg {
            display: block;
            width: 6px !important;
            height: 6px !important;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .invoice-title {
            font-size: 35px;
            font-weight: 400;
            color: #000000;
            line-height: 1;
            text-transform: uppercase;
        }

        .invoice-meta {
            font-size: 13px;
            line-height: 1.45;
            color: #0f172a;
            margin-top: 7px;
        }

        .invoice-meta-line {
            white-space: nowrap !important;
        }

        .divider {
            border-top: 1px solid #cbd5e1;
            margin: 13px 0;
            height: 1px;
        }

        .section-title {
            font-weight: 800;
            font-size: 16px;
            margin-bottom: 5px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .bill-table td {
            vertical-align: middle;
        }

        .customer {
            font-size: 14px;
            line-height: 1.45;
            color: #0f172a;
            word-break: break-word;
        }

        .customer-label {
            font-weight: 700;
        }

        .bill-rule {
            border-top: 1px solid #cbd5e1;
            width: 92%;
            margin: 5px 0 9px;
        }

        .qr-placeholder {
            width: 58px;
            height: 58px;
            border: 4px solid #111827;
            margin: 0 auto;
            text-align: center;
            font-size: 9px;
            line-height: 50px;
            color: #111827;
            font-weight: 800;
        }

        .qr-code {
            display: block;
            width: 58px;
            height: 58px;
            margin: 0 auto;
        }

        .cod-box {
            border: 0;
            border-radius: 0;
            padding: 11px 8px;
            text-align: center;
            color: #0f172a;
            min-height: 58px;
            text-decoration: none;
        }

        .cod-label {
            color: #64748b;
            font-size: 13px;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 7px;
            text-decoration: none;
        }

        .cod-amount {
            font-size: 22px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: .4px;
            text-decoration: none;
            border: 0;
        }

        .items {
            margin-top: 8px;
            border: 0;
            table-layout: fixed;
        }

        .items th {
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 5px 4px;
            color: #0f172a;
            background: #f1f5f9;
        }

        .items td {
            font-size: 13px;
            padding: 5px 4px;
            border-bottom: 1px solid #cbd5e1;
        }

        .item-name {
            line-height: 1.25;
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
            width: 46%;
            margin-left: auto;
            margin-top: 7px;
            font-size: 13px;
        }

        .summary td {
            padding: 4px 0;
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
            padding-top: 7px;
        }

        .footer-line {
            display: none;
            margin-top: 12px;
            border-top: 1px solid #cbd5e1;
            padding-top: 9px;
            font-size: 11px;
            text-align: center;
            color: #64748b;
            letter-spacing: .8px;
        }

        @if ($isSingleInvoice)
        /* A6 landscape: two complete invoices print on one A4 sheet. */
        .single-invoice { font-size: 10px !important; }
        .single-invoice .invoice-box {
            /* 194 + 16 mm padding = one 210 x 148.5 mm A5 half-sheet. */
            width: 194mm;
            min-height: 132.5mm;
            padding: 8mm;
            page-break-after: auto;
            page-break-inside: avoid;
        }
        .single-invoice .invoice-box + .invoice-box { page-break-before: auto; }
        .single-invoice .company-logo-cell { width: 15%; padding-right: 7px; }
        .single-invoice .company-info-cell { width: 48%; }
        .single-invoice .invoice-meta-cell { width: 37%; }
        .single-invoice .brand-logo { max-width: 70px; max-height: 32px; }
        .single-invoice .brand { font-size: 14px !important; }
        .single-invoice .shop-title { font-size: 19px !important; line-height: 1.06 !important; }
        .single-invoice .shop-line { font-size: 12px !important; line-height: 1.5 !important; }
        .single-invoice .contact-icon { width: 11px !important; height: 11px !important; min-width: 11px !important; max-width: 11px !important; min-height: 11px !important; max-height: 11px !important; margin-right: 4px; vertical-align: -2px; }
        .single-invoice .contact-icon svg { width: 11px !important; height: 11px !important; stroke: #e91e63 !important; }
        .single-invoice .invoice-title { font-size: 31px !important; line-height: 1 !important; }
        .single-invoice .invoice-meta { font-size: 12px !important; line-height: 1.45 !important; margin-top: 7px !important; white-space: nowrap !important; }
        .single-invoice .divider { margin: 13px 0 !important; }
        .single-invoice .section-title { font-size: 16px !important; margin-bottom: 6px !important; }
        .single-invoice .customer { font-size: 13px !important; line-height: 1.48 !important; }
        .single-invoice .bill-rule { margin: 5px 0 9px !important; }
        .single-invoice .qr-placeholder { width: 57px; height: 57px; border-width: 3px; font-size: 9px; line-height: 49px; }
        .single-invoice .qr-code { width: 57px; height: 57px; }
        .single-invoice .cod-box { min-height: 60px; padding: 11px 8px; border: 1px solid #0f172a; border-radius: 3px; }
        .single-invoice .cod-label { font-size: 12px !important; margin-bottom: 7px !important; letter-spacing: .8px !important; }
        .single-invoice .cod-amount { font-size: 24px !important; }
        .single-invoice .items { margin-top: 8px; }
        .single-invoice .items th,
        .single-invoice .items td { font-size: 11px !important; padding: 6px 4px !important; }
        .single-invoice .items th { font-size: 11px !important; padding: 6px 4px !important; }
        .single-invoice .items th { white-space: nowrap; }
        .single-invoice .item-name { line-height: 1.22 !important; }
        .single-invoice .summary { font-size: 11px !important; margin-top: 7px !important; }
        .single-invoice .summary td { padding: 4px 0 !important; }
        .single-invoice .summary-total td { padding-top: 7px !important; }
        .single-invoice .footer-line { display: block; font-size: 10px; margin-top: 12px; padding-top: 8px; }
        @endif
    </style>
</head>

<body class="{{ $isSingleInvoice ? 'single-invoice' : 'multi-invoice' }}">
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
        $formatInvoicePrice = function ($amount) {
            $currencySymbol = (string) (getSetting('currencySymbol') ?? '$');
            $currencyCode = strtoupper((string) (getSetting('currencyCode') ?? ''));

            // The bundled PDF fonts do not contain the Bengali taka glyph.
            return $currencyCode === 'BDT' || bin2hex($currencySymbol) === 'e09fb3'
                ? 'BDT '.formatPrice($amount, false, false)
                : formatPrice($amount);
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
                    $totalQty = $order->orderItems->sum('qty');
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
                @endphp

                @for ($copy = 0; $copy < ($isSingleInvoice ? 2 : 1); $copy++)
                <div class="invoice-box">
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
                                <div class="shop-line">
                                    <span class="contact-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" stroke="#e91e63"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 5.18 2 2 0 0 1 4.11 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 10.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92z"/></svg>
                                    </span>
                                    {{ getSetting('systemPhone') ?? '01888830301' }}
                                </div>
                                <div class="shop-line">
                                    <span class="contact-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" stroke="#e91e63"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                    </span>
                                    Elephant Road, Dhaka
                                </div>
                            </td>
                            <td class="invoice-meta-cell">
                                <div class="invoice-title">Invoice</div>
                                <div class="invoice-meta">
                                    <div class="invoice-meta-line">Invoice Number: {{ getSetting('order_code_prefix') }}{{ $order->orderGroup?->code ?? $order->order_code }}</div>
                                    <div class="invoice-meta-line">Invoice Date: {{ date('d M, Y', strtotime($order->created_at)) }}</div>
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
                            <td style="width: 18%; text-align: center;">
                                @if (filled($qrCodeImage))
                                    <img src="{{ $qrCodeImage }}" alt="Order QR code" class="qr-code">
                                @else
                                    <div class="qr-placeholder">QR</div>
                                @endif
                            </td>
                            <td style="width: 37%; padding-left: 7px;">
                                <div class="cod-box">
                                    <div class="cod-label">{{ $paymentMethod }}</div>
                                    <div class="cod-amount">COD: {{ formatPrice($codAmount, false, false) }} BDT</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="divider"></div>

                    <div class="section-title" style="text-transform: none;">Product Details</div>
                    <table class="items">
                        <thead>
                            <tr>
                                <th style="width: 9%;">S.No</th>
                                <th style="width: 36%; text-align: left;">Product</th>
                                <th style="width: 16%;">Size</th>
                                <th style="width: 13%;">Quantity</th>
                                <th style="width: 13%; text-align: right;">Unit price</th>
                                <th style="width: 13%; text-align: right;">Total price</th>
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
                                        @if ($item->productVariation?->code)
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
                                <td class="label">Delivery Charge</td>
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
                @endfor
    @endforeach
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('INVOICE') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Baloo+Bhaijaan+2:wght@400;500&family=Hanuman:wght@300;400;700&family=Hind+Siliguri:wght@400;500&family=Kanit:wght@400;500&family=Open+Sans:wght@400;500&family=Roboto:wght@400;500&display=swap');

        * {
            box-sizing: border-box;
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
            text-align: left;
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
    </style>
</head>

<body>
    @php
        $orderGroup = $orderGroup;
        $transaction = $orderGroup->transaction;
        $systemName = getSetting('systemName') ?? (function_exists('shopName') ? shopName() : 'Business Name');
        $systemAddress = getSetting('systemAddress') ?? '';
        $systemPhone = getSetting('phone') ?? '';
        $systemEmail = getSetting('email') ?? '';
        $logoFileId = getSetting('websiteHeaderLogo');
        $logo = null;
        $safeLogoPath = null;
        if ($logoFileId) {
            $mediaFile = \App\Models\MediaFile::find($logoFileId);
            if ($mediaFile && $mediaFile->media_file) {
                $logo = $mediaFile->media_file;
                $safeLogoPath = \App\Services\EmailImageService::makeEmailSafeLogo($logo);
            }
        }
        $paymentMethod = $transaction->payment_method ?? '';
        $paymentMethodLabels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'cash_on_delivery' => 'Cash on Delivery',
            'card_on_delivery' => 'Card on Delivery',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'razorpay' => 'Razorpay',
            'paystack' => 'PayStack',
            'flutterwave' => 'Flutterwave',
            'bank_transfer' => 'Bank Transfer',
        ];
    @endphp

    <div style="max-width: 800px; margin: 0 auto; padding: 40px;">
        <table style="width: 100%; margin-bottom: 40px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <h1 style="font-size: 42px; font-weight: 800; margin: 0 0 20px 0; text-transform: uppercase; letter-spacing: -1px;">INVOICE</h1>
                    <p style="font-size: 16px; line-height: 1.6; margin: 0 0 5px 0;">
                        <strong>{{ translate('Invoice Number') }}:</strong> {{ getSetting('order_code_prefix') }}{{ $orderGroup->code }}
                    </p>
                    <p style="font-size: 16px; line-height: 1.6; margin: 0;">
                        <strong>{{ translate('Invoice Date') }}:</strong> {{ date('d M, Y', strtotime($orderGroup->created_at)) }}
                    </p>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    @if ($safeLogoPath)
                        <img src="{{ $message->embed($safeLogoPath) }}" alt="Logo" style="display: inline-block; max-height: 70px; width: auto; margin-left: auto;">
                    @endif
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 40px;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 50px;">
                    <h2 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin: 0 0 12px 0; color: #5b5b5b;">{{ $systemName }}</h2>
                    <p style="font-size: 14px; white-space: pre-line; margin: 0 0 5px 0;">{{ $systemAddress }}</p>
                    @if ($systemPhone)
                        <p style="font-size: 14px; margin: 0 0 5px 0;">{{ $systemPhone }}</p>
                    @endif
                    @if ($systemEmail)
                        <p style="font-size: 14px; margin: 0;">{{ $systemEmail }}</p>
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <h2 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin: 0 0 12px 0; color: #5b5b5b;">BILL TO</h2>
                    <p style="font-weight: 700; font-size: 14px; margin: 0 0 5px 0;">{{ $orderGroup->billing_name }}</p>
                    <p style="font-size: 14px; white-space: pre-line; margin: 0 0 5px 0;">{{ $orderGroup->billing_address }}</p>
                    @if ($orderGroup->phone)
                        <p style="font-size: 14px; margin: 0;">{{ $orderGroup->phone }}</p>
                    @endif
                    @if ($orderGroup->billing_email)
                        <p style="font-size: 14px; margin: 0;">{{ $orderGroup->billing_email }}</p>
                    @endif
                    @if ($orderGroup->shipping_address && $orderGroup->shipping_address !== $orderGroup->billing_address)
                        <div style="margin-top: 20px;">
                            <h2 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin: 0 0 12px 0; color: #5b5b5b;">SHIP TO</h2>
                            <p style="font-weight: 700; font-size: 14px; margin: 0 0 5px 0;">{{ $orderGroup->shipping_name }}</p>
                            <p style="font-size: 14px; white-space: pre-line; margin: 0 0 5px 0;">{{ $orderGroup->shipping_address }}</p>
                            @if ($orderGroup->phone)
                                <p style="font-size: 14px; margin: 0;">{{ $orderGroup->phone }}</p>
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <table style="width: 100%; border: 1px solid #000; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 12px 15px; font-weight: 700; font-size: 14px; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: left;">Item & Description</th>
                    <th style="padding: 12px 15px; font-weight: 700; font-size: 14px; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: right; width: 100px;">Unit Price</th>
                    <th style="padding: 12px 15px; font-weight: 700; font-size: 14px; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center; width: 60px;">Qty</th>
                    <th style="padding: 12px 15px; font-weight: 700; font-size: 14px; border-bottom: 1px solid #000; text-align: right; width: 120px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderGroup->orders as $order)
                    @foreach ($order->orderItems as $item)
                        @php
                            $product = $item->productVariation->product ?? null;
                        @endphp
                        @if ($product)
                            <tr style="border-bottom: 1px solid #000;">
                                <td style="padding: 12px 15px; font-size: 14px; border-right: 1px solid #000;">
                                    {{ $product->CollectTranslation('name') }}
                                </td>
                                <td style="padding: 12px 15px; font-size: 14px; border-right: 1px solid #000; text-align: right;">
                                    {{ formatPrice($item->unit_price) }}
                                </td>
                                <td style="padding: 12px 15px; font-size: 14px; border-right: 1px solid #000; text-align: center;">
                                    {{ $item->qty }}
                                </td>
                                <td style="padding: 12px 15px; font-size: 14px; text-align: right;">
                                    <strong>{{ formatPrice($item->total_price) }}</strong>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between;">
            <div style="flex: 1;">
            </div>
            <div style="width: 250px;">
                <table style="width: 100%; border: 1px solid #000;">
                    <tr style="border-bottom: 1px solid #000;">
                        <td style="padding: 8px 15px; font-size: 14px; text-align: left;">{{ translate('Sub-Total') }}</td>
                        <td style="padding: 8px 15px; font-size: 14px; text-align: right;">{{ formatPrice($transaction->amount) }}</td>
                    </tr>
                    @if ($transaction->tax_amount > 0)
                        <tr style="border-bottom: 1px solid #000;">
                            <td style="padding: 8px 15px; font-size: 14px; text-align: left;">{{ translate('Tax') }}</td>
                            <td style="padding: 8px 15px; font-size: 14px; text-align: right;">{{ formatPrice($transaction->tax_amount) }}</td>
                        </tr>
                    @endif
                    @if ($transaction->shipping_charge_amount > 0)
                        <tr style="border-bottom: 1px solid #000;">
                            <td style="padding: 8px 15px; font-size: 14px; text-align: left;">{{ translate('Shipping') }}</td>
                            <td style="padding: 8px 15px; font-size: 14px; text-align: right;">{{ formatPrice($transaction->shipping_charge_amount) }}</td>
                        </tr>
                    @endif
                    @if ($transaction->discount_amount > 0)
                        <tr style="border-bottom: 1px solid #000;">
                            <td style="padding: 8px 15px; font-size: 14px; text-align: left;">{{ translate('Discount') }}</td>
                            <td style="padding: 8px 15px; font-size: 14px; text-align: right;">-{{ formatPrice($transaction->discount_amount) }}</td>
                        </tr>
                    @endif
                    @if ($transaction->coupon_discount_amount > 0)
                        <tr style="border-bottom: 1px solid #000;">
                            <td style="padding: 8px 15px; font-size: 14px; text-align: left;">{{ translate('Coupon Discount') }}</td>
                            <td style="padding: 8px 15px; font-size: 14px; text-align: right;">-{{ formatPrice($transaction->coupon_discount_amount) }}</td>
                        </tr>
                    @endif
                    <tr style="background: #f5f5f5;">
                        <td style="padding: 12px 15px; font-weight: 700; font-size: 16px; text-align: left;">{{ translate('TOTAL') }}</td>
                        <td style="padding: 12px 15px; font-weight: 700; font-size: 16px; text-align: right;">{{ formatPrice($transaction->total_amount) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="margin-top: 80px; padding-top: 30px; border-top: 1px solid #e4e4e4;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        @if ($paymentMethod)
                            <h3 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin: 0 0 12px 0;">{{ translate('PAYMENT METHOD') }}</h3>
                            <p style="font-size: 14px;">{{ $paymentMethodLabels[$paymentMethod] ?? $paymentMethod }}</p>
                        @endif
                    </td>
                    <td style="width: 50%; vertical-align: top; text-align: right;">
                        <h3 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin: 0 0 12px 0;">{{ translate('PREPARED BY') }}</h3>
                        <p style="font-weight: 700; font-size: 14px; margin: 0 0 5px 0;">{{ $systemName }}</p>
                        <p style="font-size: 14px; margin: 0;">{{ translate('Sales Department') }}</p>
                        @if (getSetting('terms_and_conditions'))
                            <div style="margin-top: 20px;">
                                <h3 style="font-weight: 700; font-size: 16px; text-transform: uppercase; margin: 0 0 12px 0;">{{ translate('TERMS & CONDITIONS') }}</h3>
                                <p style="font-size: 14px; white-space: pre-line; max-width: 300px; margin-left: auto;">{{ getSetting('terms_and_conditions') }}</p>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>

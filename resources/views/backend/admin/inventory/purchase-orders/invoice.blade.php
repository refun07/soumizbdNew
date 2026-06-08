<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Purchase Invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=JetBrains+Mono:wght@400;500&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #1c1917;
            background: #ffffff;
            line-height: 1.6;
            margin: 0;
            padding: 24px 16px;
        }

        p {
            padding: 0;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    @php
        $supplier = $purchaseOrder->supplier;
        $logoText = substr($systemName ?? 'EP', 0, 2);
        $logoHtml = '';
        if ($logo && file_exists($logo)) {
            $logoHtml =
                '
<div style="width:140px; height:140px; display:flex; align-items:center; justify-content:center;">
  <img src="' .
                $logo .
                '" alt="logo" style="max-width:140px; max-height:140px; display:block;">
</div>';
        } else {
            $logoHtml =
                '<div style="width: 140px; height: 140px; background: #1c1917; display: flex; align-items: center; justify-content: center; border-radius: 8px;"><span style="color: #fafaf9; font-size: 36px; font-weight: 700; letter-spacing: 2px;">' .
                $logoText .
                '</span></div>';
        }
    @endphp

    <table style="width: 100%;">
        <tr>
            <td style="vertical-align: top;">
                <h1
                    style="font-size: 48px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: -2px; line-height: 1; color: #1c1917;">
                    {{ translate('Purchase Invoice') }}</h1>
                <div style="width: 60px; height: 3px; background: #1c1917; margin-top: 12px;"></div>
                <div style="margin-top: 24px; font-size: 15px; line-height: 1.8;">
                    <p style="margin-bottom: 4px;"><strong
                            style="color: #44403c; font-weight: 600;">{{ translate('Invoice Number') }}:</strong> <span
                            style=", monospace;">#PO-{{ $purchaseOrder->reference_code }}</span>
                    </p>
                    <p><strong style="color: #44403c; font-weight: 600;">{{ translate('Invoice Date') }}:</strong>
                        {{ date('d M, Y', $purchaseOrder->date) }}</p>
                </div>
            </td>
            <td style="vertical-align: top; text-align: right;">
                {!! $logoHtml !!}
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td style="vertical-align: top; width: 50%;">
                <h2
                    style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1.5px; color: #78716c;">
                    {{ translate('From') }}</h2>
                <p style="font-weight: 700; font-size: 16px; color: #1c1917; margin-bottom: 8px;">
                    {{ $systemName ?? 'BUSINESS NAME' }}</p>
                @if (!empty($systemAddress))
                    <p style="font-size: 14px; white-space: pre-line; color: #57534e; line-height: 1.6;">
                        {{ $systemAddress }}</p>
                @endif
            </td>
            <td style="vertical-align: top; width: 50%; text-align: right;">
                <h2
                    style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1.5px; color: #78716c;">
                    {{ translate('Supplier') }}</h2>
                <p style="font-weight: 700; font-size: 16px; color: #1c1917; margin-bottom: 8px;">
                    {{ $supplier?->name ?? '-' }}</p>
                @if (!empty($supplier?->phone))
                    <p style="font-size: 14px; color: #57534e; line-height: 1.6;">{{ $supplier->phone }}</p>
                @endif
                @if (!empty($supplier?->email))
                    <p style="font-size: 14px; color: #57534e; line-height: 1.6;">{{ $supplier->email }}</p>
                @endif
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 24px; border: 1px solid #e7e5e4;">
        <tr>
            <td
                style="padding: 10px 14px; font-size: 13px; color: #57534e; border-right: 1px solid #e7e5e4; width: 25%;">
                <strong>{{ translate('Warehouse') }}</strong><br>{{ $purchaseOrder->warehouse?->name ?? '-' }}
            </td>
            <td
                style="padding: 10px 14px; font-size: 13px; color: #57534e; border-right: 1px solid #e7e5e4; width: 25%;">
                <strong>{{ translate('Status') }}</strong><br>{{ ucfirst($purchaseOrder->status) }}
            </td>
            <td
                style="padding: 10px 14px; font-size: 13px; color: #57534e; border-right: 1px solid #e7e5e4; width: 25%;">
                <strong>{{ translate('Payment Status') }}</strong><br>{{ ucfirst($purchaseOrder->payment_status) }}
            </td>
            <td style="padding: 10px 14px; font-size: 13px; color: #57534e; width: 25%;">
                <strong>{{ translate('Due') }}</strong><br>{{ formatPrice($purchaseOrder->due) }}
            </td>
        </tr>
    </table>

    <table
        style="width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1.5px solid #d6d3d1; margin-top: 40px; border-radius: 4px; overflow: hidden;">
        <thead>
            <tr style="background: #f5f5f4; border-bottom: 2px solid #d6d3d1;">
                <th
                    style="font-weight: 700; font-size: 13px; padding: 16px 20px; text-align: left; border-right: 1px solid #e7e5e4; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Item') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 16px 20px; text-align: right; border-right: 1px solid #e7e5e4; width: 110px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Unit Price') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 16px 20px; text-align: center; border-right: 1px solid #e7e5e4; width: 70px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Qty') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 16px 20px; text-align: right; border-right: 1px solid #e7e5e4; width: 90px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Tax') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 16px 20px; text-align: right; border-right: 1px solid #e7e5e4; width: 90px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c;">
                    {{ translate('Discount') }}</th>
                <th
                    style="font-weight: 700; font-size: 13px; padding: 16px 20px; text-align: right; width: 150px; text-transform: uppercase; letter-spacing: 0.5px; color: #44403c; white-space: nowrap;">
                    {{ translate('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrder->orders as $item)
                @php
                    $product = $item->productVariation?->product;
                    $variationName = '';
                    if ($item->productVariation?->code) {
                        $variationName = generateVariationName($item->productVariation->code);
                    }
                @endphp
                <tr style="border-bottom: 1px solid #e7e5e4;">
                    <td
                        style="text-align: left; padding: 14px 20px; font-size: 14px; border-right: 1px solid #e7e5e4; color: #1c1917; vertical-align: top;">
                        {{ $product?->CollectTranslation('name') ?? translate('Product not found') }}
                        @if ($variationName)
                            <div style="font-size: 12px; color: #78716c;">{{ $variationName }}</div>
                        @endif
                    </td>
                    <td
                        style="text-align: right; padding: 14px 20px; font-size: 14px; border-right: 1px solid #e7e5e4; , monospace; color: #1c1917;">
                        {{ formatPrice($item->unit_price) }}</td>
                    <td
                        style="text-align: center; padding: 14px 20px; font-size: 14px; border-right: 1px solid #e7e5e4; , monospace; color: #1c1917;">
                        {{ $item->qty }}</td>
                    <td
                        style="text-align: right; padding: 14px 20px; font-size: 14px; border-right: 1px solid #e7e5e4; , monospace; color: #1c1917;">
                        {{ formatPrice($item->tax) }}</td>
                    <td
                        style="text-align: right; padding: 14px 20px; font-size: 14px; border-right: 1px solid #e7e5e4; , monospace; color: #1c1917;">
                        {{ formatPrice($item->discount) }}</td>
                    <td
                        style="text-align: right; padding: 14px 20px; font-size: 14px; , monospace; color: #1c1917; font-weight: 500; white-space: nowrap;">
                        {{ formatPrice($item->grand_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 100%;"></td>
            <td style="width: 460px; vertical-align: top;">
                <table
                    style="width: 100%; border: 1.5px solid #d6d3d1; background: #fff; border-radius: 4px; overflow: hidden; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td
                            style="text-align: left; padding: 12px 20px; font-size: 16px; color: #57534e; width: 60%; white-space: nowrap;">
                            {{ translate('Sub Total') }}</td>
                        <td
                            style="text-align: right; padding: 12px 20px; font-size: 16px; color: #1c1917; , monospace; width: 40%; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->sub_total) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td
                            style="text-align: left; padding: 12px 20px; font-size: 16px; color: #57534e; white-space: nowrap;">
                            {{ translate('Tax') }}</td>
                        <td
                            style="text-align: right; padding: 12px 20px; font-size: 16px; color: #1c1917; , monospace; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->tax_value) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td
                            style="text-align: left; padding: 12px 20px; font-size: 16px; color: #57534e; white-space: nowrap;">
                            {{ translate('Shipping') }}</td>
                        <td
                            style="text-align: right; padding: 12px 20px; font-size: 16px; color: #1c1917; , monospace; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->shipping) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td
                            style="text-align: left; padding: 12px 20px; font-size: 16px; color: #57534e; white-space: nowrap;">
                            {{ translate('Discount') }}</td>
                        <td
                            style="text-align: right; padding: 12px 20px; font-size: 16px; color: #1c1917; , monospace; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->discount) }}</td>
                    </tr>
                    <tr style="background: #f5f5f4; border-bottom: 1px solid #e7e5e4;">
                        <td
                            style="text-align: left; padding: 16px 20px; font-weight: 700; font-size: 18px; color: #1c1917; text-transform: uppercase; white-space: nowrap;">
                            {{ translate('Grand Total') }}</td>
                        <td
                            style="text-align: right; padding: 16px 20px; font-weight: 700; font-size: 18px; color: #1c1917; , monospace; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->grand_total) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e7e5e4;">
                        <td
                            style="text-align: left; padding: 12px 20px; font-size: 16px; color: #57534e; white-space: nowrap;">
                            {{ translate('Paid') }}</td>
                        <td
                            style="text-align: right; padding: 12px 20px; font-size: 16px; color: #15803d; , monospace; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->paid) }}</td>
                    </tr>
                    <tr>
                        <td
                            style="text-align: left; padding: 12px 20px; font-size: 16px; color: #57534e; white-space: nowrap;">
                            {{ translate('Due') }}</td>
                        <td
                            style="text-align: right; padding: 12px 20px; font-size: 16px; color: #b91c1c; , monospace; white-space: nowrap;">
                            {{ formatPrice($purchaseOrder->due) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if (!empty($purchaseOrder->note))
        <div style="margin-top: 40px;">
            <h3
                style="font-weight: 700; font-size: 12px; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1.5px; color: #78716c;">
                {{ translate('Note') }}</h3>
            <p style="font-size: 14px; color: #57534e; white-space: pre-line;">{{ $purchaseOrder->note }}</p>
        </div>
    @endif
</body>

</html>

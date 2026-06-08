<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ getSetting('systemName') }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        @font-face {
            font-family: 'Roboto';
            src: url("{{ asset('fonts/Roboto-Regular.ttf') }}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-size: 0.75rem;
            font-family: 'Roboto';
            font-weight: normal;
            direction: ltr;
            text-align: left;
            padding: 0;
            margin: 0;
            color: #232323;
            background: #f5f5f5;
        }

        table {
            width: 100%;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .btn {
            background: #4CAF50;
            color: #fff;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}" height="40" style="display:inline-block; margin-bottom: 15px">
            <h1 style="margin: 0; color: #333;">{{ translate('Stock Request Fulfilled') }}</h1>
        </div>

        <div style="padding: 20px 0;">
            <p style="color: #666; line-height: 1.6; font-size: 14px;">
                {{ translate('Good news! The product you requested is now in stock and available for purchase.') }}
            </p>

            <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin: 25px 0; display: flex; gap: 20px; align-items: center;">
                @if($request->product_image)
                <img src="{{ asset($request->product_image) }}" alt="{{ $request->product_name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                @endif
                <div>
                    <strong style="font-size: 16px; color: #333;">{{ $request->product_name }}</strong>
                    @if($request->product_sku)
                    <p style="margin: 5px 0 0; color: #888; font-size: 13px;">{{ translate('SKU') }}: {{ $request->product_sku }}</p>
                    @endif
                    @if(isset($request->product_details['variation_name']))
                    <p style="margin: 5px 0 0; color: #666; font-size: 14px;">{{ translate('Variation') }}: {{ $request->product_details['variation_name'] }}</p>
                    @endif
                    @if(isset($request->product_details['price']))
                    <p style="margin: 5px 0 0; color: #4CAF50; font-size: 16px; font-weight: bold;">{{ translate('Price') }}: {{ formatPrice($request->product_details['price']) }}</p>
                    @endif
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $productUrl }}" class="btn">{{ translate('Shop Now') }}</a>
            </div>

            <p style="color: #999; font-size: 13px; text-align: center;">{{ translate('Thank you for your patience. We appreciate your interest in our products.') }}</p>
        </div>

        <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; text-align: center;">
            <p style="color: #999; font-size: 12px; margin: 0;">{{ getSetting('systemName') }}<br>{{ getSetting('systemAddress') }}</p>
        </div>
    </div>
</body>
</html>

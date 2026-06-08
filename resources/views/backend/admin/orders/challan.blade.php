<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('CHALLAN') }}</title>
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
            border: 1px solid #e4e4e4;
        }

        td,
        th {
            text-align: left;
            padding: 10px;

        }

        .visibleMobile {
            display: none;

        }

        .hiddenMobile {
            display: block;

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

        @page {
            footer: custom-footer;
        }
    </style>
</head>

<body>
    @php
        $orderGroup = $order->orderGroup;
        $user = $order->user;
    @endphp

    {{-- header start --}}
    <table style="width: 100%; table-layout: fixed;border:none">
        <tr>
            <td style="text-align: start; width: 50%;">
                @if (file_exists($logo))
                    <img src="{{ $logo }}" alt="logo" style="max-height: 70px;">
                @endif
            </td>

            <td style="text-align: right; width: 50%;">
                @if ($order->shop?->name)
                    <div
                        style="display: inline-flex; align-items: center; gap: 6px; color: #5b5b5b; justify-content: flex-end;">
                        <span>{{ $order->shop->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" width="15"
                            height="15">
                            <path style="fill:#5b5b5b;"
                                d="M4 4h16l1.5 4.5H2.5L4 4zm17 6v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V10h18z" />
                        </svg>
                    </div>
                @endif

                @if ($order->shop?->user?->email)
                    <div
                        style="display: inline-flex; align-items: center; gap: 6px; color: #5b5b5b; justify-content: flex-end;">
                        <span>{{ $order->shop->user->email }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" width="15"
                            height="15">
                            <path style="fill:#5b5b5b;"
                                d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 2v.01L12 13 4 6.01V6h16zM4 18v-9l8 6 8-6v9H4z" />
                        </svg>
                    </div>
                @endif

                @if ($order->shop?->user?->phone)
                    <div style="color: #5b5b5b; ">
                        <span>{{ $order->shop->user->phone }}</span>
                        <svg fill="currentColor" viewBox="0 0 24 24" width="15" height="15">
                            <path style="fill:#5b5b5b;"
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.84 19.84 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.84 19.84 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72 12.52 12.52 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.52 12.52 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </div>
                @endif
            </td>
        </tr>

        <tr class="visibleMobile">
            <td height="10"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center">
                <p style="font-size: 20px; text-align: center; font-weight: bold; color: #5b5b5b; ">
                    {{ translate('DELIVERY CHALLAN') }}</p>
            </td>
        </tr>

    </table>
    {{-- header end --}}

    {{-- billing and shipping start --}}
    <table class="table" style="width: 100%;margin-bottom:20px">
        <tbody style="display: table-header-group">
            <tr>
                <td colspan="4" style="border-right: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Challan No') }}: <span style="font-weight: normal">{{ $order->order_code }}</span>
                    </p>
                </td>
                <td colspan="4" style="border-bottom: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Challan Date') }}: <span
                            style="font-weight: normal">{{ date('d/m/Y', strtotime($order->created_at)) }}</span>
                    </p>
                </td>
            </tr>

            <tr style=" ">
                <td colspan="4" style="width: 300px;border-right: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Customer Details') }}</p>

                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ $user?->name ?? '' }}</p>
                    <p style="font-size: 12px; color: #5b5b5b; line-height: 24px; vertical-align: top;">
                        {{ $orderGroup->shipping_address }}
                        <br>
                        <span>{{ translate('Phone') }}: {{ $orderGroup->phone }}</span>

                        @if ($order->orderGroup->alternative_phone_no)
                            <br>
                            {{ translate('Alternative Phone') }}: {{ $orderGroup->alternative_phone_no }}
                        @endif
                    </p>

                </td>

                <td colspan="4" style="width: 300px;">


                </td>

            </tr>


        </tbody>
    </table>
    {{-- billing and shipping end --}}

    {{-- item details start --}}
    <table class="table" style="width: 100%;margin-bottom:20px">
        <tbody style="display: table-header-group">
            <tr>
                <td style="border-right: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('SL') }}
                    </p>
                </td>
                <td style="border-bottom: 1px solid #e4e4e4;border-right: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Product Name') }}
                    </p>
                </td>
                <td style="border-bottom: 1px solid #e4e4e4;border-right: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Manufacturer') }}
                    </p>
                </td>
                <td style="border-bottom: 1px solid #e4e4e4;border-right: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Details Information') }}
                    </p>
                </td>
                <td style="border-bottom: 1px solid #e4e4e4;">
                    <p
                        style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                        {{ translate('Quantity') }}
                    </p>
                </td>
            </tr>
            @foreach ($order->orderItems as $key => $item)
                @php
                    $product = $item->productVariation->product;
                @endphp
                <tr style=" ">
                    <td
                        style="font-size: 12px; color: #5b5b5b; border-right: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4; ">
                        {{ $key + 1 }}</td>
                    <td style="font-size: 12px; color: #5b5b5b;  line-height: 18px;border-bottom: 1px solid #e4e4e4; border-right: 1px solid #e4e4e4;  vertical-align: top; padding:10px 5px;"
                        class="article">
                        <div>{{ $product->CollectTranslation('name') }}</div>
                        <div class="text-muted">
                            @foreach (generateVariationCombinations($item->productVariation->combinations) as $variation)
                                <span class="fs-xs">
                                    {{ $variation['name'] }}:
                                    @foreach ($variation['values'] as $value)
                                        {{ $value['name'] }}
                                    @endforeach
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </td>

                    <td
                        style="font-size: 12px; color: #5b5b5b; border-right: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4; ">

                        {{ $product->brand?->name ?? '' }}
                    </td>
                    <td
                        style="font-size: 12px; color: #5b5b5b; border-right: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4; ">

                        -
                    </td>
                    <td style="font-size: 12px; color: #5b5b5b; border-bottom: 1px solid #e4e4e4; ">{{ $item->qty }}
                        {{ $product->unit?->name ?? '' }}
                    </td>

                </tr>
            @endforeach


        </tbody>
    </table>
    {{-- item details end --}}


    {{-- Memo start --}}
    <table class="" style="border:none">
        <tbody>
            @if ($order->memo_date)
                <tr>
                    <td class="text-left">
                        <p
                            style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                            {{ translate('MEMO Date') }}: <span
                                style="font-weight: normal">{{ date('d/m/Y', strtotime($order->memo_date)) }}</span>
                        </p>
                </tr>
            @endif
            @if ($order->memo_no)
                <tr class="">
                    <td class="text-left">
                        <p
                            style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                            {{ translate('MEMO No') }}: <span style="font-weight: normal">{{ $order->memo_no }}</span>
                        </p>
                    </td>
                </tr>
            @endif
            @if ($order->mushak_challan_no)
                <tr>
                    <td class="text-left">
                        <p
                            style="font-size: 12px; font-weight: bold; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                            {{ translate('Mushak Challan No') }}: <span
                                style="font-weight: normal">{{ $order->mushak_challan_no }}</span>
                        </p>
                    </td>
                </tr>
            @endif


        </tbody>
    </table>

    {{-- Memo end --}}
    {{-- footer start --}}
    <htmlpagefooter name="custom-footer">
        <table class="table" style="width: 100%;margin-bottom:20px;border:none">
            <tbody>
                <tr>
                    <td>
                        <p style="border-top: 1px dotted black; font-size: 12px; color: #5b5b5b; ">
                            {{ translate('Prepared by') }}
                        </p>
                    </td>
                    <td>
                        <p style="border-top: 1px dotted black; font-size: 12px; color: #5b5b5b; ">
                            {{ translate('Approved by') }}
                        </p>
                    </td>
                    <td>
                        <p style="border-top: 1px dotted black; font-size: 12px; color: #5b5b5b; ">
                            {{ translate('Checked by') }}
                        </p>
                    </td>
                    <td>
                        <p style="border-top: 1px dotted black; font-size: 12px; color: #5b5b5b; ">
                            {{ translate('Received by') }}
                        </p>
                    </td>
                </tr>
                @if ($order->shop?->address)
                    <tr>
                        <td colspan="4" style="text-align: center;">
                            <div
                                style="display: inline-flex; align-items: center; gap: 6px; color: #5b5b5b; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                    viewBox="0 0 24 24">
                                    <path style="fill: #5b5b5b"
                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                                </svg>
                                <span>
                                    {{ $order->shop?->address }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @endif




            </tbody>
        </table>
    </htmlpagefooter>
    {{-- footer end --}}
</body>

</html>

@extends('layouts.' . $route)

@section('title')
    {{ translate('Add New Return Request') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Add New Return Request') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route($route . '.return-requests.index') }}"
                class="font-bold ">{{ translate('Return Request List') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Add New Return Request') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="theme-table">
            <div
                class="relative card__title border-none theme-table__filter flex flex-col lg:flex-row xl:items-center justify-between gap-1">

                <div class="group relative !mt-4" tabindex="0">
                    <x-backend.inputs.text label="{{ translate('Search Invoice') }}" class="return-refund-search-input"
                        name="search" value="{{ $searchKey }}" autocomplete="off"
                        placeholder="{{ translate('Search With Invoice ID') }}" :isRequired="false" />

                    <div
                        class="return-refund-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">

                        <div class="block py-4 px-6 bg-primary/[.03] text-muted font-bold">
                            <span class="uppercase">{{ translate('Search Results') }}</span>
                        </div>

                        <div class="divide-y divide-[#78787829] return-refund-search-results">

                        </div>
                    </div>


                </div>

                <div class="!mt-4" id="request-type-wrapper">
                    <x-backend.inputs.select name="request_type" label="{{ translate('Request Type') }}" class="filterSelect" :isRequired="true">
                        <x-backend.inputs.select-option name="{{ translate('Return') }}" value="return" />
                        <x-backend.inputs.select-option name="{{ translate('Refund') }}" value="refund" />
                    </x-backend.inputs.select>
                </div>

            </div>

            <form action="{{ route($route . '.return-requests.store') }}" method="POST" enctype="multipart/form-data"
                class="return-refund-form">
                @csrf

                <input type="hidden" name="request_type" value="return" id="request-type-hidden">

                <table class="footable w-full [&_td]:">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th>
                                {{ translate('Product') }}
                            </th>
                            <th data-breakpoints="xs sm md">
                                {{ translate('QTY') }}
                            </th>
                            <th>
                                {{ translate('Reason') }}
                            </th>
                            <th>
                                {{ translate('Note') }}
                            </th>
                            <th>
                                {{ translate('Media') }}
                            </th>
                        </tr>
                    </thead>
                    @if ($order && count($order->orderItems) > 0)
                        <tbody>
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="invoice_no" value="{{ $order->order_code }}">
                            <input type="hidden" name="customer_id" value="{{ $order->user_id }}">
                            @foreach ($order->orderItems as $index => $item)
                                @php
                                    $variation = $item->productVariation;
                                    $product = $variation->product;
                                @endphp
                                <tr>
                                    <td>
                                        <label class="flex gap-3 items-center">

                                            <x-backend.inputs.checkbox
                                                name="items[{{ $index }}][product_variation_id]"
                                                value="{{ $variation->id }}" class="return-product-checkbox" />
                                            <input type="hidden" name="items[{{ $index }}][product_id]"
                                                value="{{ $product->id }}" />
                                            <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt=""
                                                class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                            <p class="line-clamp-2 max-w-[350px]">{{ $product->name }}</p>

                                        </label>
                                        <input type="hidden" name="items[{{ $index }}][price]"
                                            value="{{ $item->total_price }}">

                                    </td>
                                    <td>
                                        <x-backend.inputs.number class="max-w-32"
                                            name="items[{{ $index }}][quantity]" value="{{ $item->qty }}"
                                            max="{{ $item->qty }}" :isRequired="false" />
                                    </td>
                                    <td>
                                        <x-backend.inputs.select name="items[{{ $index }}][reason]"
                                            class="filterSelect" :isRequired="false">
                                            <x-backend.inputs.select-option name="{{ translate('Reason') }}"
                                                value="" />
                                            <x-backend.inputs.select-option name="{{ translate('Size Issue') }}"
                                                value="Size Issue" />
                                            <x-backend.inputs.select-option name="{{ translate('Color Issue') }}"
                                                value="Color Issue" />
                                            <x-backend.inputs.select-option name="{{ translate('Wrong Product') }}"
                                                value="Wrong Product" />
                                            <x-backend.inputs.select-option name="{{ translate('Delayed Delivery') }}"
                                                value="Delayed Delivery" />
                                            <x-backend.inputs.select-option name="{{ translate('Defected Product') }}"
                                                value="Defected" />
                                            <x-backend.inputs.select-option name="{{ translate('Expired') }}"
                                                value="Expired" />
                                            <x-backend.inputs.select-option name="{{ translate('Broken') }}"
                                                value="Broken" />
                                            <x-backend.inputs.select-option name="{{ translate('Damage Product') }}"
                                                value="Damage" />

                                        </x-backend.inputs.select>
                                    </td>
                                    <td>
                                        <x-backend.inputs.text name="items[{{ $index }}][note]" class="max-w-48"
                                            :isRequired="false" />
                                    </td>
                                    <td>
                                        <x-backend.inputs.file name="items[{{ $index }}][media_path]"
                                            :isRequired="false" />
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    @endif
                </table>
                @if ($order && count($order->orderItems) > 0)
                    <div class="flex justify-end pr-12 pb-6 ">
                        <x-backend.inputs.button buttonText="Add Return Request" type="submit" class="submit-btn" />
                    </div>
                @endif
            </form>



        </div>
    </div>
@endsection


@section('scripts')
    <x-backend.inc.return-refund-order-search-scripts />

    <script>
        "use strict";

        $(document).on('change', 'select[name="request_type"]', function() {
            $('#request-type-hidden').val($(this).val());
        });
    </script>
@endsection

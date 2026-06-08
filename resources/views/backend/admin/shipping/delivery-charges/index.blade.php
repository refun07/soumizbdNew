@extends('layouts.admin')

@section('title')
    {{ translate('Update Delivery Charges') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Delivery Charges') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Zones') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-12">
        <div class="md:col-span-8 card">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                <div></div>
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
            </div>

            <form action="{{ route(routePrefix() . '.delivery-charges.store') }}" method="POST">
                @csrf
                <table class="w-full ">
                    <thead class="uppercase text-left" style="background-color: rgb(var(--background-primary-light) / 10">
                        <tr>
                            <th class="p-4">
                                {{ translate('NO.') }}
                            </th>
                            <th class="p-4">
                                {{ translate('Zone') }}
                            </th>

                            <th class="py-4">
                                {{ translate('Shipping Class') }}
                            </th>

                            <th class="py-4">
                                {{ translate('Delivery Charge') }}
                            </th>

                            <th class="py-4">
                                {{ translate('MRate') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="px-4">
                        @foreach ($zones as $key => $zone)
                            @foreach ($shippingClasses as $index => $class)
                                <tr class="border-b border-gray-300">
                                    @if ($loop->first)
                                        <td class="align-top px-4 py-2" rowspan="{{ $shippingClasses->count() }}">
                                            {{ $key + 1 + ($zones->currentPage() - 1) * $zones->perPage() }}
                                        </td>
                                        <td class="align-top px-4 py-2" rowspan="{{ $shippingClasses->count() }}">
                                            {{ $zone->name }}
                                        </td>
                                        <input type="hidden" name="zone_ids[]" value="{{ $zone->id }}" />
                                    @endif
                                    <td class="px-4 py-2">{{ $class->name }}</td>
                                    <td class="px-4 py-2">

                                        <input type="number"
                                            name="delivery_charges[{{ $zone->id }}][{{ $class->id }}]"
                                            class="border border-gray-300 rounded-md px-2 py-1 w-32" placeholder="0Tk"
                                            value="{{ optional($zone->shippingChargeByClass($class->id, shopId()))->charge ?? '' }}"
                                            onwheel="this.blur()" />
                                    </td>
                                    <td class="px-4 py-2">

                                        <input type="number" min="1"
                                            name="mrate[{{ $zone->id }}][{{ $class->id }}]"
                                            class="border border-gray-300 rounded-md px-2 py-1 w-24" placeholder="-"
                                            value="{{ optional($zone->shippingChargeByClass($class->id, shopId()))->mrate ?? '' }}"
                                            onwheel="this.blur()" />
                                        <p class="text-xs text-gray-500 mt-1">{{ translate('Items per charge') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

                <div class="pe-8 flex justify-end mt-4">
                    <x-backend.inputs.button buttonText="Save Delivery Charges" type="submit" />
                </div>

            </form>

            <div class="card__footer">
                {{ $zones->links() }}
            </div>
        </div>
    </div>
@endsection

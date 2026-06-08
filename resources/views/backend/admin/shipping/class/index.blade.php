@extends('layouts.' . $route)

@section('title')
    {{ translate('Shipping Class') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Shipping Class') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Class') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-12">
        <div class="md:col-span-8 card">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                <div></div>
                {{-- @can('create_products') --}}
                <x-backend.inputs.link href="{{ route(routePrefix() . '.shipping-classes.create') }}">
                    <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span>
                    {{ translate('ADD SHIPPING CLASS') }}
                </x-backend.inputs.link>
                {{-- @else
                    <div></div>
                @endcan --}}
            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10 px-3">
                    <tr>

                        <th>
                            {{ translate('CLASS NAMES') }}
                        </th>

                        <th class="max-w-[130px]">
                            {{ translate('DESCRIPTIONS') }}
                        </th>
                        <th>
                            {{ translate('ASSIGNED TO') }}
                        </th>
                        <th>
                            {{ translate('OPTIONS') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shippingClasses as $key => $shippingClass)
                        <tr>
                            <td>{{ $shippingClass->name }}</td>
                            <td class="text-gray-600">{{ $shippingClass->description }}</td>
                            <td>
                                <strong>{{ str_pad($shippingClass->products_count, 2, '0', STR_PAD_LEFT) }}
                                    Products</strong>
                            </td>
                            <td>
                                <a class="text-blue-400 hover:text-blue-500 pr-4"
                                    href="{{ route($route . '.shipping-classes.edit', $shippingClass->id) }}">
                                    <i class="fa-regular fa-pen-to-square"></i></a>

                                <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                                    data-href="{{ route($route . '.shipping-classes.destroy', $shippingClass->id) }}"
                                    data-title="{{ translate('Are you sure you want to delete this item?') }}"
                                    data-text="{{ translate('All data related to this may get deleted.') }}"
                                    data-method="DELETE" data-micromodal-trigger="confirm-modal">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $shippingClasses->links() }}
            </div>
        </div>
    </div>
@endsection

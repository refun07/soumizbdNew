@extends('layouts.admin')

@section('title')
    {{ translate('Damage Stocks') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Damage Stock List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Damage Stock List') }}</p>
        </div>
    </div>

    <div class="card theme-table">
        <div
            class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            @can('create_damage_stocks')
                <x-backend.inputs.link href="{{ route('admin.damageStocks.create') }}"> <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span> {{ translate('Add New') }}</x-backend.inputs.link>
            @else
                <div></div>
            @endcan

            <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]"
                placeholder="Type product name & hit enter" />
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-primary/10">
                <tr>
                    <th data-breakpoints="xs sm">#</th>
                    <th data-breakpoints="xs sm">{{ translate('Date') }}</th>
                    <th>{{ translate('Product') }}</th>
                    @if (useInventory())
                        <th>{{ translate('Warehouse') }}</th>
                    @endif
                    <th>{{ translate('Qty') }}</th>
                    <th>{{ translate('Damage Type') }}</th>
                    <th>{{ translate('Recorded By') }}</th>
                    <th data-breakpoints="xs sm" class="w-[180px]">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody class="po-order-tbody">
                @foreach ($damageStocks as $key => $damageStock)
                    <tr>
                        <td>{{ $key + 1 + ($damageStocks->currentPage() - 1) * $damageStocks->perPage() }}</td>
                        <td>
                            <div class="line-clamp-2">
                                {{ date('d M, Y', strtotime($damageStock->return_date)) }}
                            </div>
                        </td>
                        <td>
                            {{ $damageStock->productVariation?->product?->collectTranslation('name') }}
                            @if ($damageStock->productVariation?->product?->has_variation)
                                - <span class="text-orange-500 font-bold">{{ $damageStock->productVariation?->name }}</span>
                            @endif
                        </td>
                        @if (useInventory())
                            <td>{{ $damageStock->warehouse?->name ?? translate('N/A') }}</td>
                        @endif
                        <td>{{ $damageStock->quantity }}</td>
                        <td>
                            <span class="badge
                                @if ($damageStock->damage_type == 'warehouse') badge--info
                                @elseif($damageStock->damage_type == 'transportation') badge--warning
                                @elseif($damageStock->damage_type == 'expired') badge--danger
                                @else badge--secondary @endif">
                                {{ translate(ucfirst(str_replace('_', ' ', $damageStock->damage_type))) }}
                            </span>
                        </td>
                        <td>{{ $damageStock->creator?->name ?? translate('System') }}</td>
                        <td>
                            <div class="option-dropdown w-full" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>
                                <div class="option-dropdown__options">
                                    <ul>
                                        <li>
                                            <a href="{{ route('admin.damageStocks.show', ['damage' => $damageStock->id]) }}"
                                                class="option-dropdown__option">
                                                {{ translate('Details') }}
                                            </a>
                                        </li>
                                        @can('delete_damage_stocks')
                                            <li>
                                                <a href="javascript:void(0)"
                                                    onclick="confirm_modal('{{ route('admin.damageStocks.destroy', ['damage' => $damageStock->id]) }}')"
                                                    class="option-dropdown__option">
                                                    {{ translate('Delete') }}
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="card__footer">
            {{ $damageStocks->links() }}
        </div>
    </div>
@endsection

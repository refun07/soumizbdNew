@extends('layouts.admin')

@section('title')
    {{ translate('Stock Requests') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-bell"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Requests') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Requests') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="theme-table">
            <div class="card__title border-none theme-table__filter flex flex-col lg:flex-row xl:items-center justify-between gap-3">
                <div></div>
                <div class="flex items-center gap-2">
                    @php($pendingCount = \App\Models\StockRequest::pending()->count())
                    @if($pendingCount > 0)
                        <span class="inline-block px-3 py-1 rounded text-sm font-medium text-white bg-badge-warning">
                            {{ $pendingCount }} {{ translate('Pending Requests') }}
                        </span>
                    @endif
                </div>
            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th data-breakpoints="xs sm md">{{ translate('Product DETAILS') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Customer') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Qty') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Status') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Date') }}</th>
                        <th data-breakpoints="xs sm md" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="inline-flex items-center gap-4">
                                    <div class="max-xs:hidden">
                                        @if($item->product_image)
                                            <img src="{{ asset($item->product_image) }}" alt=""
                                                class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        @else
                                            <div class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md bg-gray-100 flex items-center justify-center">
                                                <i class="fa-solid fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="line-clamp-2 max-w-[250px]">
                                        {{ $item->product_name }}
                                        @if($item->product_sku)
                                            <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                                        @endif
                                        @if(isset($item->product_details['variation_name']))
                                            <p class="text-xs text-info">{{ $item->product_details['variation_name'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->user_id && $item->user)
                                    <div>
                                        {{ $item->user->name }}
                                        <p class="text-xs text-gray-500">{{ $item->user->email }}</p>
                                    </div>
                                @else
                                    <div>
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium text-white bg-badge-info">
                                        {{ translate('Guest') }}
                                    </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $item->email ?? '-' }}</p>
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{ $item->quantity }}
                            </td>
                            <td>
                                @if($item->status == 0)
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium text-white bg-badge-warning">
                                        {{ translate('Pending') }}
                                    </span>
                                @elseif($item->status == 1)
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium text-white bg-badge-success">
                                        {{ translate('Fulfilled') }}
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-medium text-white bg-badge-danger">
                                        {{ translate('Cancelled') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ date('d M, Y', strtotime($item->created_at)) }}
                            </td>
                            <td>
                                <div class="option-dropdown w-[130px]" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            <li>
                                                <a href="{{ route('admin.stock-requests.show', $item->id) }}"
                                                    class="option-dropdown__option">
                                                    <i class="fa-solid fa-eye mr-1"></i>{{ translate('View') }}
                                                </a>
                                            </li>
                                            @can('delete_stock_requests')
                                                <li>
                                                    <form action="{{ route('admin.stock-requests.destroy', $item->id) }}" method="POST"
                                                          onsubmit="return confirm('Are you sure?');" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="option-dropdown__option w-full text-start border-0 bg-transparent cursor-pointer">
                                                            <i class="fa-solid fa-trash mr-1"></i>{{ translate('Delete') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-muted">
                                {{ translate('No stock requests found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="card__footer">
                {{ $items->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
@endsection

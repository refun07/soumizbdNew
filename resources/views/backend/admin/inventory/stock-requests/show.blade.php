@extends('layouts.admin')

@section('title')
    {{ translate('Stock Request Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-solid fa-bell"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Request Details') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.stock-requests.index') }}" class="text-muted">{{ translate('Stock Requests') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Details') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 4xl:grid-cols-4 gap-10">
        <div class="col-span-1 4xl:col-span-3 space-y-10">
            <div class="card">
                <h4 class="card__title">{{ translate('Product Information') }}</h4>

                <div class="card__content">
                    <div class="flex flex-wrap items-center gap-6">
                        @if($stockRequest->product_image)
                            <div class="border border-border rounded-md p-2 aspect-square">
                                <img src="{{ asset($stockRequest->product_image) }}" alt="{{ $stockRequest->product_name }}"
                                     class="w-20 h-20 lg:w-24 lg:h-24 rounded-md object-cover"
                                     onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">
                            </div>
                        @else
                            <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-md bg-gray-100 flex items-center justify-center border border-border">
                                <i class="fa-solid fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h4 class="text-lg font-medium">{{ $stockRequest->product_name }}</h4>
                            @if($stockRequest->product_sku)
                                <p class="text-muted text-sm mt-1">SKU: {{ $stockRequest->product_sku }}</p>
                            @endif
                            @if(isset($stockRequest->product_details['variation_name']))
                                <span class="inline-block mt-2 px-3 py-0.5 rounded text-xs font-medium bg-badge-info text-white">
                                    {{ $stockRequest->product_details['variation_name'] }}
                                </span>
                            @endif
                            @if(isset($stockRequest->product_details['price']))
                                <p class="text-secondary font-semibold text-lg mt-3">
                                    {{ translate('Price') }}: {{ formatPrice($stockRequest->product_details['price']) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h4 class="card__title">{{ translate('Request Information') }}</h4>

                <div class="card__content">
                    <div class="grid lg:grid-cols-2 gap-6">
                        <table class="w-full [&_td]:py-2">
                            <tr>
                                <td class="text-xs text-muted uppercase w-40">{{ translate('Requested Quantity') }}:</td>
                                <td class="text-sm font-medium">{{ $stockRequest->quantity }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-muted uppercase">{{ translate('Status') }}:</td>
                                <td>
                                    @if($stockRequest->status == 0)
                                        <span class="px-3 py-0.5 rounded text-xs font-medium text-white bg-badge-warning">
                                            {{ $stockRequest->status_text }}
                                        </span>
                                    @elseif($stockRequest->status == 1)
                                        <span class="px-3 py-0.5 rounded text-xs font-medium text-white bg-badge-success">
                                            {{ $stockRequest->status_text }}
                                        </span>
                                    @else
                                        <span class="px-3 py-0.5 rounded text-xs font-medium text-white bg-badge-danger">
                                            {{ $stockRequest->status_text }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <table class="w-full [&_td]:py-2">
                            <tr>
                                <td class="text-xs text-muted uppercase w-40">{{ translate('Requested Date') }}:</td>
                                <td class="text-sm">{{ date('d M, Y', strtotime($stockRequest->created_at)) }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-muted uppercase">{{ translate('Last Updated') }}:</td>
                                <td class="text-sm">{{ date('d M, Y', strtotime($stockRequest->updated_at)) }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($stockRequest->customer_note || $stockRequest->admin_note)
                        <div class="mt-8 pt-6 border-t border-border">
                            @if($stockRequest->customer_note)
                                <div class="mb-6">
                                    <p class="text-xs text-muted uppercase mb-2">{{ translate('Customer Note') }}</p>
                                    <p class="bg-gray-50 dark:bg-gray-800 p-4 rounded-md text-sm">{{ $stockRequest->customer_note }}</p>
                                </div>
                            @endif
                            @if($stockRequest->admin_note)
                                <div>
                                    <p class="text-xs text-muted uppercase mb-2">{{ translate('Admin Note') }}</p>
                                    <p class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-md text-sm">{{ $stockRequest->admin_note }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-span-1 space-y-6">
            <div class="card">
                <h4 class="card__title">{{ translate('Customer Information') }}</h4>

                <div class="card__content">
                    @if($stockRequest->user_id && $stockRequest->user)
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-user text-primary text-xl"></i>
                            </div>
                            <h5 class="font-medium">{{ $stockRequest->user->name }}</h5>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium bg-badge-success text-white">
                                {{ translate('Registered Customer') }}
                            </span>
                        </div>
                        <table class="w-full [&_td]:py-2 text-sm">
                            <tr>
                                <td class="text-xs text-muted">{{ translate('Email') }}:</td>
                                <td class="text-right">{{ $stockRequest->user->email }}</td>
                            </tr>
                            @if($stockRequest->user->phone)
                                <tr>
                                    <td class="text-xs text-muted">{{ translate('Phone') }}:</td>
                                    <td class="text-right">{{ $stockRequest->user->phone }}</td>
                                </tr>
                            @endif
                        </table>
                    @else
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 rounded-full bg-info/10 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-user-clock text-info text-xl"></i>
                            </div>
                            <h5 class="font-medium">{{ translate('Guest User') }}</h5>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium bg-badge-info text-white">
                                {{ translate('Guest') }}
                            </span>
                        </div>
                        @if($stockRequest->email || $stockRequest->phone)
                            <table class="w-full [&_td]:py-2 text-sm">
                                @if($stockRequest->email)
                                    <tr>
                                        <td class="text-xs text-muted">{{ translate('Email') }}:</td>
                                        <td class="text-right">{{ $stockRequest->email }}</td>
                                    </tr>
                                @endif
                                @if($stockRequest->phone)
                                    <tr>
                                        <td class="text-xs text-muted">{{ translate('Phone') }}:</td>
                                        <td class="text-right">{{ $stockRequest->phone }}</td>
                                    </tr>
                                @endif
                            </table>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card">
                <h4 class="card__title">{{ translate('Update Status') }}</h4>

                <div class="card__content">
                    <form action="{{ route('admin.stock-requests.updateStatus', $stockRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <x-backend.inputs.select label="Status" name="status" :labelInline="false" wrapperClass="mb-4">
                            <option value="0" {{ $stockRequest->status == 0 ? 'selected' : '' }}>{{ translate('Pending') }}</option>
                            <option value="1" {{ $stockRequest->status == 1 ? 'selected' : '' }}>{{ translate('Fulfilled') }}</option>
                            <option value="2" {{ $stockRequest->status == 2 ? 'selected' : '' }}>{{ translate('Cancelled') }}</option>
                        </x-backend.inputs.select>

                        <x-backend.inputs.textarea label="Admin Note" name="admin_note" :labelInline="false"
                            placeholder="Add a note (optional)" rows="3"
                            :value="$stockRequest->admin_note" />

                        <div class="mt-6">
                            <x-backend.inputs.button type="submit" variant="primary" buttonText="Update Status" class="w-full" />
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <h4 class="card__title">{{ translate('Actions') }}</h4>

                <div class="card__content space-y-3">
                    @if(isset($stockRequest->product_details['slug']))
                        <x-backend.inputs.link href="{{ route('product', $stockRequest->product_details['slug']) }}"
                            variant="secondary" target="_blank" class="w-full text-center">
                            <i class="fa-solid fa-external-link-alt mr-2"></i>
                            {{ translate('View Product') }}
                        </x-backend.inputs.link>
                    @endif
                    @if(auth()->user()->can('delete_stock_requests'))
                        <form action="{{ route('admin.stock-requests.destroy', $stockRequest->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <x-backend.inputs.button type="submit" variant="danger" buttonText="Delete Request" class="w-full" />
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

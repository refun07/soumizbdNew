@extends('layouts.admin')

@section('title')
    {{ translate('Update Seller') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Seller') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.sellers.index') }}" class="font-bold ">{{ translate('Sellers') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Update') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- start::product-form -->
    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-5">
            <div class="card">
                <div class="card__content">
                    <form action="{{ route('admin.sellers.update', $seller->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @php
                            $langKey = config('app.default_language');
                        @endphp
                        <input type="hidden" name="lang_key" value="{{ $langKey }}">


                        <div class="space-y-3">
                            <x-backend.inputs.text label="Name" name="name" placeholder="Type seller name"
                                value="{{ $seller->name }}" />
                            <x-backend.inputs.text label="Email" name="email" placeholder="Type seller email"
                                value="{{ $seller->email }}" />


                            <x-backend.inputs.number label="Admin Commission Percentage" name="admin_commission_percentage"
                                placeholder="Type admin commission rate" min="0" step="0.001"
                                value="{{ $seller->shop->admin_commission_percentage }}" />

                            <div class="theme-input-group w-full hidden">
                                <label for="password" class="theme-input-label pt-0">
                                    {{ translate('Password') }}
                                </label>
                                <div class="theme-input-wrapper flex gap-3">
                                    <input type="password" id="password" name="password" class='theme-input'
                                        placeholder="{{ translate('Type seller password') }}" />
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <x-backend.inputs.button buttonText="Save Seller" type="submit" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end::product-form -->
@endsection

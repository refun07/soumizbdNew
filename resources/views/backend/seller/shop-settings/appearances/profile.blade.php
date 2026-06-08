@extends('layouts.seller')

@section('title')
    {{ translate('Update Shop') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Shop') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Shop Profile') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12 gap-4">
        <div class="xl:col-span-6">
            <div class="card">
                <div class="card__content">
                    <form action="{{ route('seller.shops.updateProfile') }}" method="POST">
                        @csrf
                        <div class="space-y-3">

                            <x-backend.inputs.text name="name" label="Name" value="{{ $shop->name }}" />

                            <x-backend.inputs.text name="tagline" label="Tagline" value="{{ $shop->tagline }}"
                                :isRequired="false" />

                            <x-backend.inputs.file name="logo" label="Logo" value="{{ $shop->logo }}" />

                            <x-backend.inputs.file name="banner" label="Banner" value="{{ $shop->banner }}" />

                            <x-backend.inputs.select label="Manage Stock By" name="manage_stock_by">
                                <x-backend.inputs.select-option name="Without Inventory" value="default"
                                    selected="{{ $shop->manage_stock_by }}" />
                                <x-backend.inputs.select-option name="Inventory" value="inventory"
                                    selected="{{ $shop->manage_stock_by }}" />
                            </x-backend.inputs.select>

                            <x-backend.inputs.number name="default_shipping_charge" label="Default Shipping Charge"
                                value="{{ $shop->default_shipping_charge }}" placeholder="0" :isRequired="false" />

                            <x-backend.inputs.seo metaTitle="{{ $shop->meta_title }}"
                                metaDescription="{{ $shop->meta_description }}" metaKeywords="{{ $shop->meta_keywords }}"
                                metaImage="{{ $shop->meta_image }}" />
                            <div class="flex justify-end pt-2">
                                <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="xl:col-span-6">
            <div class="card">
                <div class="card__content">
                    <h3 class="font-bold">
                        {{ translate('Before changing the Stock Management feature, please be aware of these-') }}</h3>
                    <ul class="list-inside list-none mt-3">
                        <li>
                            <span class=" text-orange-400"><i class="far fa-exclamation-circle"></i></span>
                            {{ translate('If you use "Inventory" for stock management, you need to add stocks for each products by creating purchase orders.') }}
                        </li>
                        <li>
                            <span class=" text-orange-400"><i class="far fa-exclamation-circle"></i></span>
                            {{ translate('You will have access to the menus of inventory in the sidebar.') }}
                        </li>

                        <div class="border-t mt-3"></div>
                        <li class="mt-3">
                            <span class=" text-orange-400"><i class="far fa-exclamation-circle"></i></span>
                            {{ translate('If you manage your stock "Without Inventory", you can add product stocks while adding a product.') }}
                        </li>
                        <li>
                            <span class=" text-orange-400"><i class="far fa-exclamation-circle"></i></span>
                            {{ translate('Stocks will be added to the Default warehouse if you have that, else the stock will not be added.') }}
                        </li>

                        <div class="border-t mt-3"></div>
                        <li class="mt-3">
                            <span class=" text-orange-400"><i class="far fa-exclamation-circle"></i></span>
                            {{ translate('If you switch from "Inventory" to "Without Inventory", only the stock from your default warehouse will be available in the website.') }}
                        </li>

                        <li>
                            <span class=" text-orange-400"><i class="far fa-exclamation-circle"></i></span>
                            {{ translate('You can transfer your stock from other Warehouse to Default Warehouse before switching to "Without Inventory".') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

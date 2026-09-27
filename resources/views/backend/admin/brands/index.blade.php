@extends('layouts.admin')

@section('title')
    {{ translate('Brand List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Brand List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Brand List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card">
                <div class="card__title border-none">
                    <div class="flex flex-col md:flex-row justify-end gap-3">
                        <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
                    </div>
                </div>

                <div class="card__content pt-0">
                    <div class="grid xl:grid-cols-2 gap-3 lg:gap-5">
                        @foreach ($brands as $key => $brand)
                            <div
                                class="py-3 px-4 bg-background-primary-light rounded-md flex items-center gap-3.5 relative">
                                <img src="{{ uploadedAsset($brand->thumbnail_image) }}" alt=""
                                    class="w-[70px] h-[80px] rounded-md"
                                    onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">

                                <div class="grow">
                                    <h4 class="font-bold">{{ $brand->collectTranslation('name') }}</h4>
                                    <p class="text-xs mt-1 whitespace-nowrap">
                                        {{ translate('Total Products') }}:
                                        <span class="text-muted ml-0.5">{{ $brand->products_count }}</span>
                                    </p>
                                </div>

                                <div class="flex gap-3 absolute top-3 right-3">

                                    @can('edit_brands')
                                        <a class="text-stone-300 hover:text-stone-500"
                                            href="{{ route('admin.brands.edit', ['brand' => $brand->id, 'lang_key' => config('app.default_language')]) }}&translate">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    @endcan

                                    @can('delete_brands')
                                        <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                                            data-href="{{ route('admin.brands.destroy', $brand->id) }}"
                                            data-title="{{ translate('Are you sure you want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card__footer pt-0">
                    {{ $brands->links() }}
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_brands')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Brand') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.brand-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection

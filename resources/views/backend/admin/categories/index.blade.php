@extends('layouts.admin')

@section('title')
    {{ translate('Categories') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Category List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.categories.index') }}" class="font-bold "> {{ translate('Categories') }}</a>

            @if ($parentCategory)
                <span class="text-primary">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                <a href="?parent_id={{ $parentCategory->id }}" class="text-muted">
                    {{ $parentCategory->collectTranslation('name') }}</a>
            @endif
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="card__title theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

            @can('create_categories')
                <x-backend.inputs.link href="{{ route('admin.categories.create') }}"> <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span> {{ translate('Add New') }}</x-backend.inputs.link>
            @else
                <div></div>
            @endcan

            <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]">
                @if ($parentCategory)
                    <input type="hidden" name="parent_id" value="{{ $parentCategory->id }}" />
                @endif
            </x-backend.forms.search-form>
        </div>

        <div class="card__content">
            <div class="mb-3 flex items-center gap-3 text-xs">
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 text-muted">
                    <span class="text-secondary-light">
                        <i class="fa-solid fa-house-chimney"></i>
                    </span>
                    <span>{{ translate('Main Categories') }}</span>
                </a>

                @if ($parentCategory)
                    <span class="text-muted">
                        <i class="fa-solid fa-angle-right"></i>
                    </span>
                    <a href="?parent_id={{ $parentCategory->id }}" class="flex items-center gap-1">
                        {{ $parentCategory->collectTranslation('name') }}
                    </a>
                @endif

            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-3 lg:gap-5">
                @foreach ($categories as $key => $category)
                    <div class="bg-background-primary-light rounded-md py-5 px-7">
                        <div class="flex items-center gap-2.5">
                            <div>
                                <img src="{{ uploadedAsset($category->collectTranslation('thumbnail_image')) }}"
                                    alt=""
                                    class="w-10 lg:w-12 aspect-square rounded-full border border-secondary-light"
                                    onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                            </div>

                            <div class="grow leading-tight">
                                @if ($parentCategory)
                                    <p class="text-muted">{{ $parentCategory->collectTranslation('name') }}
                                    </p>
                                @endif
                                <h4 class="uppercase font-medium">{{ $category->collectTranslation('name') }}</h4>
                            </div>

                            <div class="flex gap-3">

                                @can('edit_categories')
                                    <a class="text-stone-300 hover:text-stone-500"
                                        href="{{ route('admin.categories.edit', ['category' => $category->id, 'lang_key' => config('app.default_language')]) }}&translate">
                                        <i class="fa-regular fa-pen-to-square"></i></a>
                                @endcan

                                @can('delete_categories')
                                    <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                                        data-href="{{ route('admin.categories.destroy', $category->id) }}"
                                        data-title="{{ translate('Are you sure you want to delete this item?') }}"
                                        data-text="{{ translate('All data related to this may get deleted.') }}"
                                        data-method="DELETE" data-micromodal-trigger="confirm-modal">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                @endcan
                            </div>
                        </div>

                        <div class="flex justify-between mt-4">
                            <span>
                                {{ translate('Serial') }}:
                                <span class="text-muted">{{ $category->sorting_order_level }}</span>
                            </span>

                            <span>
                                <a @if ($category->children_categories_count) href="?parent_id={{ $category->id }}" @endif>
                                    {{ translate('Sub-categories') }}
                                    <span class="text-muted">({{ $category->children_categories_count }})</span>
                                </a>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card__footer pt-0">
            {{ $categories->links() }}
        </div>
    </div>
@endsection

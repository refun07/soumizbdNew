@extends('layouts.admin')

@section('title')
    {{ translate('Blog Categories') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Blog Category List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.blogCategories.index') }}" class="font-bold ">{{ translate('Blog Categories') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Blog Category List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->
    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card">
                <div class="theme-table">
                    <div
                        class="card__title border-none theme-table__filter flex flex-col lg:flex-row xl:items-center justify-between gap-3">
                        <div></div>
                        <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[320px]"
                            placeholder="Type & hit enter" />
                    </div>


                    <table class="blog-list-table footable w-full [&_td]:">
                        <thead class="uppercase text-left bg-primary/10">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    {{ translate('Category DETAILS') }}
                                </th>

                                <th data-breakpoints="xs sm md" class="w-[130px]">
                                    {{ translate('Options') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($blogCategories as $key => $blogCategory)
                                <tr>
                                    <td>{{ $key + 1 + ($blogCategories->currentPage() - 1) * $blogCategories->perPage() }}
                                    </td>
                                    <td class="">
                                        <div class="inline-flex items-center gap-4">
                                            <div class=" line-clamp-2 max-w-[350px]">
                                                {{ $blogCategory->collectTranslation('name') }}
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="option-dropdown w-[130px]" tabindex="0">
                                            <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                                <span>{{ translate('Actions') }}</span>
                                            </div>

                                            <div class="option-dropdown__options">
                                                <ul>

                                                    @can('edit_blog_categories')
                                                        <li>
                                                            <a href="{{ route('admin.blogCategories.edit', ['blog_category' => $blogCategory->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                                class="option-dropdown__option">
                                                                {{ translate('Edit') }}
                                                            </a>
                                                        </li>
                                                    @endcan

                                                    @can('delete_blog_categories')
                                                        <li>
                                                            <x-backend.inputs.delete-link
                                                                href="{{ route('admin.blogCategories.destroy', $blogCategory->id) }}" />
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
                        {{ $blogCategories->links() }}
                    </div>
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_badges')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Category') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.blog-category-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection

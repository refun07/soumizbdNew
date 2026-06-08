@extends('layouts.admin')

@section('title')
    {{ translate('Blogs') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Blog List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.blogs.index') }}" class="font-bold ">{{ translate('Blogs') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Blog List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col lg:flex-row xl:items-center justify-between gap-3">

                @can('create_blogs')
                    <x-backend.inputs.link href="{{ route('admin.blogs.create') }}">
                        <span class="text-xl">
                            <i class="fal fa-plus"></i>
                        </span>
                        {{ translate('Add New') }}
                    </x-backend.inputs.link>
                @else
                    <div></div>
                @endcan


                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[1000px]"
                    placeholder="Type & hit enter">
                    <x-backend.inputs.select name="isPublished" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All blogs') }}" value=""
                            selected="{{ Request::get('isPublished') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Published') }}" value="1"
                            selected="{{ Request::get('isPublished') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Unpublished') }}" value="0"
                            selected="{{ Request::get('isPublished') }}" />
                    </x-backend.inputs.select>
                </x-backend.forms.search-form>
            </div>


            <table class="blog-list-table footable w-full [&_td]:">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            {{ translate('Blog DETAILS') }}
                        </th>

                        <th data-breakpoints="xs sm">{{ translate('Category') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm md" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blogs as $key => $blog)
                        <tr>
                            <td>{{ $key + 1 + ($blogs->currentPage() - 1) * $blogs->perPage() }}</td>
                            <td class="">
                                <div class="inline-flex items-center gap-4">

                                    <div class="max-xs:hidden">
                                        <img src="{{ uploadedAsset($blog->thumbnail_image) }}" alt=""
                                            class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                    </div>
                                    <div class=" line-clamp-2 max-w-[350px]">
                                        {{ $blog->collectTranslation('title') }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $blog->blogCategory?->collectTranslation('name') }}
                            </td>

                            <td>
                                @can('edit_blogs')
                                    <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.blogs.status') }}"
                                        name="isActiveCheckbox" value="{{ $blog->id }}"
                                        data-status="{{ $blog->is_published }}"
                                        isChecked="{{ (int) $blog->is_published == 1 }}" />
                                @endcan
                            </td>

                            <td>
                                <div class="option-dropdown w-[130px]" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            <li>
                                                <a href="{{ url('/blogs'.'/'.$blog->slug)}}"
                                                    target="__blank"
                                                    class="option-dropdown__option">
                                                    {{ translate('View') }}
                                                </a>
                                            </li>
                                            @can('edit_blogs')
                                                <li>
                                                    <a href="{{ route('admin.blogs.edit', ['blog' => $blog->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('delete_blogs')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.blogs.destroy', $blog->id) }}" />
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
                {{ $blogs->links() }}
            </div>
        </div>
    </div>
@endsection

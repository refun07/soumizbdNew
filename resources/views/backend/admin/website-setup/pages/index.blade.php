@extends('layouts.admin')

@section('title')
    {{ translate('Pages') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Pages') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Pages') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid">
        <div class="card">
            <div class="theme-table">

                <div
                    class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                    @can('create_pages')
                        <x-backend.inputs.link href="{{ route('admin.pages.create') }}">
                            <span class="text-xl">
                                <i class="fal fa-plus"></i>
                            </span>
                            {{ translate('Add New') }}
                        </x-backend.inputs.link>
                    @else
                        <div></div>
                    @endcan
                </div>

                <div>
                    <table class="product-list-table footable w-full">
                        <thead class="uppercase text-left bg-primary/10 px-3">
                            <tr>
                                <th data-breakpoints="xs sm">
                                    #
                                </th>
                                <th>
                                    {{ translate('Name') }}
                                </th>
                                <th data-breakpoints="xs sm">
                                    {{ translate('URL') }}
                                </th>
                                <th data-breakpoints="xs sm" class="w-[130px]">
                                    {{ translate('Options') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $key => $page)
                                <tr>
                                    <td>
                                        {{ $key + 1 }}
                                    </td>
                                    <td>
                                        {{ $page->collectTranslation('title') }}
                                    </td>
                                    <td>
                                        <div class=" line-clamp-2">
                                            {{ url('/pages') . '/' . $page->slug }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="option-dropdown" tabindex="0">
                                            <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                                <span>{{ translate('Actions') }}</span>
                                            </div>

                                            <div class="option-dropdown__options">
                                                <ul>
                                                    @if ($page->type != 'special')
                                                        @can('edit_pages')
                                                            <li>
                                                                <a href="{{ route('admin.pages.edit', ['page' => $page->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                                    class="option-dropdown__option">
                                                                    {{ translate('Edit') }}
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    @elseif($page->type == 'special')
                                                        @can('edit_pages')
                                                            <li>
                                                                <a href="{{ route('admin.homepage.configure', ['id' => $page->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                                    class="option-dropdown__option">
                                                                    {{ translate('Configure') }}
                                                                </a>
                                                            </li>
                                                        @endcan
                                                    @endif

                                                    @if ($page->type == 'custom')
                                                        @can('delete_pages')
                                                            <li>
                                                                <x-backend.inputs.delete-link
                                                                    href="{{ route('admin.pages.destroy', $page->id) }}" />
                                                            </li>
                                                        @endcan
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        "use strict";

        $(".$page-type").on("change", function(e) {
            if ($(".$page-type").val() != "products") {
                $(".$page-title").attr('required', false);
                $(".$page-title").closest('.theme-input-group').addClass('hidden');
            } else {
                $(".$page-title").attr('required', true);
                $(".$page-title").closest('.theme-input-group').removeClass('hidden');
            }
        });
    </script>
@endsection

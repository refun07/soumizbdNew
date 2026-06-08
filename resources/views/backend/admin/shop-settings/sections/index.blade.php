@extends('layouts.admin')

@section('title')
    {{ translate('Home Sections') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Home Sections') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shop Settings') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Sections') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card">
                <div class="theme-table">
                    <div>
                        <table class="product-list-table footable w-full">
                            <thead class="uppercase text-left bg-primary/10 px-3">
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th data-breakpoints="xs sm md">
                                        {{ translate('Type') }}
                                    </th>
                                    <th data-breakpoints="xs sm md">
                                        {{ translate('Order') }}
                                    </th>
                                    <th data-breakpoints="xs sm" class="w-[130px]">
                                        {{ translate('Options') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shopSections as $key => $section)
                                    <tr>
                                        <td>
                                            {{ $key + 1 }}
                                        </td>
                                        <td>
                                            <div class=" line-clamp-2 capitalize">
                                                {{ $section->type }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $section->order }}
                                        </td>
                                        <td>
                                            <div class="option-dropdown" tabindex="0">
                                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                                    <span>{{ translate('Actions') }}</span>
                                                </div>

                                                <div class="option-dropdown__options">
                                                    <ul>
                                                        @can('aedit_home_sections')
                                                            <li>
                                                                <a href="{{ route('admin.shop-sections.edit', ['section' => $section->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                                    class="option-dropdown__option">
                                                                    {{ translate('Configure') }}
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete_home_sections')
                                                            <li>
                                                                <x-backend.inputs.delete-link
                                                                    href="{{ route('admin.shop-sections.destroy', $section->id) }}" />
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
                    </div>
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            @can('add_home_sections')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Section') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.shop-section-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        "use strict";

        $(".section-type").on("change", function(e) {
            if ($(".section-type").val() != "products") {
                $(".section-title").attr('required', false);
                $(".section-title").closest('.theme-input-group').addClass('hidden');
            } else {
                $(".section-title").attr('required', true);
                $(".section-title").closest('.theme-input-group').removeClass('hidden');
            }
        });
    </script>
@endsection

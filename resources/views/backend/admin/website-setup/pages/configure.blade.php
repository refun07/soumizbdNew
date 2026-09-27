@extends('layouts.admin')

@section('title')
    {{ translate('Update Page') }} | {{ getSetting('systemName') }}
@endsection

@section('p-class')
    !ps-2
@endsection

@section('content')
    <div class="grid xl:grid-cols-12 gap-8">
        <div class="xl:col-span-3">
            <div class="card">
                <div class="bg-primary text-primary-foreground rounded-t py-5 px-8 text-md">
                    {{ translate($page?->title . ' Editor') . ' - ' . $theme }}
                </div>
                @if ($page->slug == 'homepage')
                    <div class="init-accordion accordion-container border-collapse">
                        <x-backend.forms.storefront-layout-settings-form />
                        @if ($theme == 'theme-one')
                            <x-backend.forms.homepage-theme-one-form langKey="{{ $lang_key }}" />
                        @elseif ($theme == 'theme-two')
                            <x-backend.forms.homepage-theme-two-form langKey="{{ $lang_key }}" />
                        @elseif ($theme == 'theme-three')
                            <x-backend.forms.homepage-theme-three-form langKey="{{ $lang_key }}" />
                        @endif
                        @if ($theme == 'theme-one')
                            <x-backend.forms.homepage-theme-one-reviews-form />
                        @endif
                        <x-backend.forms.search-result-settings-form />
                        <x-backend.forms.header-customer-menu-settings-form />
                        <x-backend.forms.featured-brands-form langKey="{{ $lang_key }}" />
                        <x-backend.forms.dynamic-sections-form langKey="{{ $lang_key }}" />
                        <x-backend.forms.homepage-popup-form />
                    </div>
                @endif

            </div>
        </div>
        <div class="xl:col-span-9">
            <!-- start::dashboard breadcrumb -->
            <div class="dashboard-nav py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm sm:text-base font-bold">
                        {{ translate('Preview') }}
                    </span>
                </div>
            </div>
            <!-- end::dashboard breadcrumb -->
            <div class="card h-[calc(100vh-200px)] sticky top-3"> <!-- Adjusted min-h class -->
                <iframe src="{{ url('/') }}" class="w-full h-full" id="homepage-iframe"
                    title="Embedded Webpage"></iframe>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title')
    {{ translate('Brands & Collections') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-6">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Brands & Collections') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ $tab === 'collections' ? translate('Collections') : translate('Brands') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- start::tabs -->
    <div class="flex items-center gap-6 border-b border-border mb-5">
        @foreach ([
            'brands' => ['label' => translate('Brands'), 'count' => $brandsCount],
            'collections' => ['label' => translate('Collections'), 'count' => $collectionsCount],
        ] as $tabKey => $tabItem)
            <a href="{{ route('admin.brands.index', $tabKey === 'brands' ? [] : ['tab' => $tabKey]) }}"
                class="-mb-px inline-flex items-center gap-2 border-b-2 pb-3 text-sm font-bold transition {{ $tab === $tabKey ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-primary' }}">
                {{ $tabItem['label'] }}
                <span
                    class="rounded-full px-2 py-0.5 text-xs font-medium {{ $tab === $tabKey ? 'bg-primary text-white' : 'bg-primary/10 text-primary' }}">
                    {{ $tabItem['count'] }}
                </span>
            </a>
        @endforeach
    </div>
    <!-- end::tabs -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card">
                <div class="card__title border-none">
                    <form action="{{ route('admin.brands.index') }}" method="GET"
                        class="flex flex-col md:flex-row justify-end gap-3">
                        @if ($tab === 'collections')
                            <input type="hidden" name="tab" value="collections">
                        @endif
                        <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
                    </form>
                </div>

                <div class="card__content pt-0">
                    @if ($tab === 'collections')
                        @include('backend.admin.brands.partials.collection-list')
                    @else
                        @include('backend.admin.brands.partials.brand-list')
                    @endif
                </div>
                <div class="card__footer pt-0">
                    {{ $tab === 'collections' ? $collections->links() : $brands->links() }}
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_brands')
                <div class="card">
                    @if ($tab === 'collections')
                        <h4 class="card__title">{{ translate('Add New Collection') }}</h4>
                        <div class="card__content">
                            <x-backend.forms.collection-form />
                        </div>
                    @else
                        <h4 class="card__title">{{ translate('Add New Brand') }}</h4>
                        <div class="card__content">
                            <x-backend.forms.brand-form />
                        </div>
                    @endif
                </div>
            @endcan
        </div>
    </div>
@endsection

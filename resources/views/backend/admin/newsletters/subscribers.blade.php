@extends('layouts.admin')

@section('title')
    {{ translate('Subscriber List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Subscriber List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Subscriber List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5">
            <div class="card theme-table">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                    <div></div>
                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                        placeholder="Type email & hit enter" />
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Email') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Subscribed At') }}
                            </th>
                            <th data-breakpoints="xs sm" class="text-end">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscribers as $key => $subscriber)
                            <tr>
                                <td>{{ $key + 1 + ($subscribers->currentPage() - 1) * $subscribers->perPage() }}</td>

                                <td>
                                    <div class=" line-clamp-2">
                                        {{ $subscriber->email ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class=" line-clamp-2">
                                        {{ date('d M, Y', strtotime($subscriber->created_at)) }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <a href="javascript:void(0);"
                                        data-href="{{ route('admin.subscribers.destroy', $subscriber->id) }}"
                                        data-title="{{ translate('Are you sure want to delete this item?') }}"
                                        data-text="{{ translate('All data related to this may get deleted.') }}"
                                        data-method="DELETE" data-micromodal-trigger="confirm-modal"
                                        class="confirm-modal text-red-500 text-lg ms-2">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $subscribers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

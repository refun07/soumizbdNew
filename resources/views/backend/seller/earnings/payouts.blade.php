@extends('layouts.seller')

@section('title')
    {{ translate('Payouts') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Payouts') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Payouts') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5 card theme-table">
            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th data-breakpoints="xs sm">
                            #
                        </th>
                        <th>
                            {{ translate('Amount') }}
                        </th>

                        <th>
                            {{ translate('Payment Method') }}
                        </th>

                        <th data-breakpoints="xs sm">
                            {{ translate('Transaction Id') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Date') }}
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $key => $request)
                        <tr>
                            <td>{{ $key + 1 + ($requests->currentPage() - 1) * $requests->perPage() }}</td>

                            <td>
                                {{ formatPrice($request->given_amount) }}
                            </td>
                            <td>
                                <div class=" line-clamp-2 capitalize">
                                    {{ $request->payment_method }}
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2 capitalize">
                                    {{ $request->payment_details ?? '-' }}
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ date('d M, Y', strtotime($request->created_at)) }}
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection

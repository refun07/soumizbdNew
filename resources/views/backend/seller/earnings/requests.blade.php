@extends('layouts.seller')

@section('title')
    {{ translate('Payout Requests') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Payout Requests') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Payout Requests') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid grid-cols-4 gap-3 place-content-center mb-4">
        <div class="card bg-primary text-white text-center p-4">
            <div class="mb-2">
                {{ formatPrice(shop()->current_balance) }}
            </div>
            <p>
                {{ translate('Pending Balance') }}
            </p>
        </div>

        <button type="button" class="card text-center p-4" data-micromodal-trigger="seller-payout-request-modal">
            <div class="mb-2">
                <i class="fa-solid fa-plus"></i>
            </div>
            <p>{{ translate('Send Payout Request') }}</p>
        </button>
    </div>

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5 card theme-table">
            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th data-breakpoints="xs sm">
                            #
                        </th>

                        <th>
                            {{ translate('Date') }}
                        </th>

                        <th>
                            {{ translate('Amount') }}
                        </th>
                        <th>
                            {{ translate('Status') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Message') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $key => $request)
                        <tr>
                            <td>{{ $key + 1 + ($requests->currentPage() - 1) * $requests->perPage() }}</td>
                            <td>
                                {{ date('d M, Y', strtotime($request->created_at)) }}
                            </td>
                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($request->demanded_amount) }}
                                </div>
                            </td>
                            <td>
                                <span
                                    class="px-2 py-1 text-xs {{ $request->status == 'requested' ? 'bg-primary' : 'bg-red-500' }}  text-white rounded-md leading-none capitalize">
                                    {{ $request->status }}
                                </span>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ $request->additonal_info ?? '-' }}
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

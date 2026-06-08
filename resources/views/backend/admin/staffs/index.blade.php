@extends('layouts.admin')

@section('title')
    {{ translate('Staff List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Staff List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Staff List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5">
            <div class="card theme-table">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                    @can('create_staffs')
                        <x-backend.inputs.link href="{{ route('admin.staffs.create') }}"> <span class="text-xl">
                                <i class="fal fa-plus"></i>
                            </span> {{ translate('Add New') }}</x-backend.inputs.link>
                    @else
                        <div></div>
                    @endcan

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                        placeholder="Type name/email/phone & hit enter" />
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                {{ translate('Name') }}
                            </th>
                            <th>
                                {{ translate('Role') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Email') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Phone') }}
                            </th>

                            <th data-breakpoints="xs sm">
                                {{ translate('Commission') }}
                            </th>

                            <th data-breakpoints="xs sm">
                                {{ translate('Balance') }}
                            </th>

                            <th data-breakpoints="xs sm">
                                {{ translate('Banned') }}
                            </th>

                            <th data-breakpoints="xs sm" class="text-end">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staffs as $key => $staff)
                            <tr>
                                <td>{{ $key + 1 + ($staffs->currentPage() - 1) * $staffs->perPage() }}</td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($staff->avatar) }}" alt=""
                                                class="w-[70px] h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div class=" line-clamp-2">
                                            {{ $staff->name }}
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="text-stone-500 line-clamp-2">
                                        {{ $staff->role?->name ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class=" line-clamp-2">
                                        {{ $staff->email ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class=" line-clamp-2">
                                        {{ $staff->phone ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div class=" line-clamp-2">
                                        @php
                                            $mode = $staff->agent_commission_mode ?? 'percentage';
                                            $pct = $staff->agent_commission_percentage ?? 0;
                                            $fixed = $staff->agent_commission_fixed_amount ?? 0;
                                            if ($mode === 'percentage') echo $pct . '%';
                                            elseif ($mode === 'fixed') echo formatPrice($fixed);
                                            elseif ($mode === 'both_highest') echo 'max(' . $pct . '%, ' . formatPrice($fixed) . ')';
                                            elseif ($mode === 'both_additive') echo $pct . '% + ' . formatPrice($fixed);
                                            else echo $pct . '%';
                                        @endphp
                                    </div>
                                </td>

                                <td>
                                    <div class=" line-clamp-2">
                                        {{ formatPrice($staff->agent_current_balance ?? 0) }}
                                    </div>
                                </td>

                                <td>

                                    @can('edit_staffs')
                                        <x-backend.inputs.checkbox toggler="true"
                                            data-route="{{ route('admin.staffs.toggleBan') }}" variant="danger"
                                            name="isActiveCheckbox" value="{{ $staff->id }}"
                                            data-status="{{ $staff->is_banned }}"
                                            isChecked="{{ (int) $staff->is_banned == 1 }}" />
                                    @endcan
                                </td>


                                <td class="text-end">

                                    @can('edit_staffs')
                                        <a href="{{ route('admin.staffs.edit', ['staff' => $staff->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                            class="text-stone-500 text-lg">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    @endcan

                                    @can('delete_staffs')
                                        <a href="javascript:void(0);"
                                            data-href="{{ route('admin.staffs.destroy', $staff->id) }}"
                                            data-title="{{ translate('Are you sure want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal"
                                            class ='confirm-modal text-red-500 text-lg ms-2'>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $staffs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

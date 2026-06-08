@extends('layouts.admin')

@section('title')
    {{ translate('Role List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Role List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Role List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-6 gap-3">
        <div class="md:col-span-6 card">

            <div class="card__title theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                @can('create_roles_and_permissions')
                    <x-backend.inputs.link href="{{ route('admin.roles.create') }}"> <span class="text-xl">
                            <i class="fal fa-plus"></i>
                        </span> {{ translate('Add New') }}</x-backend.inputs.link>
                @else
                    <div></div>
                @endcan

                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
            </div>

            <div class="card__title border-none font-normal grid sm:grid-cols-2 4xl:grid-cols-4 gap-4">

                @foreach ($roles as $key => $role)
                    <div class="py-3 md:py-7 px-4 md:px-4 bg-background-primary-light rounded-md flex flex-col">
                        <div class="flex justify-between">
                            <div class="capitalize text-stone-500 text-sm">{{ translate('Role name') }}</div>
                            <div class="flex gap-3">
                                @php
                                    $defaultroleIds = [1];
                                @endphp

                                @if (!in_array($role->id, $defaultroleIds))
                                    @can('edit_roles_and_permissions')
                                        <a class="text-stone-300 hover:text-stone-500"
                                            href="{{ route('admin.roles.edit', ['role' => $role->id, 'lang_key' => config('app.default_language')]) }}&translate">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    @endcan
                                    @can('delete_roles_and_permissions')
                                        <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                                            data-href="{{ route('admin.roles.destroy', $role->id) }}"
                                            data-title="{{ translate('Are you sure you want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    @endcan
                                @endif

                            </div>
                        </div>
                        <div>
                            <p class="uppercase text-dark">{{ $role->name }}</p>
                        </div>

                        <div class="mt-4 flex justify-between border-t border-primary-14 pt-4 items-center text-sm">
                            @php
                                $userCount = \App\Models\User::where('role_id', $role->id)->count();
                            @endphp
                            <p>
                                {{ translate('Total Users') }}:
                                <span
                                    class="text-muted">{{ !in_array($role->id, $defaultroleIds) ? $userCount : 1 }}</span>
                            </p>
                            <div class="flex items-center justify-end capitalize gap-3">
                                @can('edit_roles_and_permissions')
                                    <label for="isActiveCheckbox-{{ $role->id }}">{{ translate('Publish') }}</label>
                                    <x-backend.inputs.checkbox id="isActiveCheckbox-{{ $role->id }}" toggler="true"
                                        data-route="{{ route('admin.roles.status') }}" name="isActiveCheckbox"
                                        value="{{ $role->id }}" data-status="{{ $role->is_active }}"
                                        isChecked="{{ (int) $role->is_active == 1 }}" />
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card__footer">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection

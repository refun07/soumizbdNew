@extends('layouts.' . routePrefix())

@section('title')
    {{ translate('Update Profile') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Update Profile') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route(routePrefix() . '.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('User Profile') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-6">
            <div class="card">
                <div class="card__content">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="space-y-3">

                            <x-backend.inputs.text name="name" label="Name" value="{{ $user->name }}" />
                            <x-backend.inputs.text name="email" label="Email" value="{{ $user->email }}" />

                            <x-backend.inputs.password name="password" label="Password" value="" :isRequired="false"
                                placeholder="*****" />
                            <x-backend.inputs.file label="Avatar" name="avatar" value="{{ $user->avatar }}" />


                            <div class="flex justify-end pt-2">
                                <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

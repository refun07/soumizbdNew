@extends('layouts.admin')

@section('title')
    {{ translate('Send Newsletters') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Send Newsletters') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Send Newsletters') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <form action="{{ route('admin.newsletters.store') }}" method="POST">
        @csrf

        <div class="grid md:grid-cols-12 gap-3">
            <div class="md:col-span-7 md:row-span-2">
                {{-- code --}}
                <div class="card">
                    <div class="card__title flex justify-between items-center">
                        {{ translate('General Informations') }}
                    </div>

                    <div class="card__content">
                        <div class="space-y-3">

                            <x-backend.inputs.text label="Subject" name="subject" placeholder="Type subject"
                                value="" />

                            <x-backend.inputs.select label="Users" name="user_emails[]"
                                data-placeholder="{{ translate('Select emails') }}" multiple
                                data-selection-css-class="multi-select2" :isRequired="false">
                                @foreach ($users as $user)
                                    <x-backend.inputs.select-option name="{{ $user->name . ' - ' . $user->email }}"
                                        value="{{ $user->email }}" />
                                @endforeach
                            </x-backend.inputs.select>
                            <x-backend.inputs.select label="Subscribers" name="subscriber_emails[]"
                                data-placeholder="{{ translate('Select subscriber emails') }}" multiple
                                data-selection-css-class="multi-select2" :isRequired="false">
                                @foreach ($subscribers as $subscriber)
                                    <x-backend.inputs.select-option name="{{ $subscriber->email }}"
                                        value="{{ $subscriber->email }}" />
                                @endforeach
                            </x-backend.inputs.select>

                            <x-backend.inputs.textarea label="Newsletter content" rich="true" name="content"
                                placeholder="Type newsletter content" value="" />
                        </div>

                    </div>
                </div>
                {{-- submit button --}}
                <div class="flex justify-end mt-6">
                    <x-backend.inputs.button buttonText="Send Newsletter" type="submit" />
                </div>
            </div>
    </form>
@endsection

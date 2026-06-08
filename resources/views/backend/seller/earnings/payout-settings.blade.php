@extends('layouts.seller')

@section('title')
    {{ translate('Payout Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Payout Settings') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Payout Settings') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <!-- start::product-form -->
    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-6">
            <div class="card">
                <div class="card__content">
                    <form action="{{ route('seller.earnings.updatePayoutSettings') }}" method="POST">
                        @csrf
                        @php
                            $langKey = config('app.default_language');
                        @endphp

                        <div class="space-y-3">

                            <div class="theme-input-group w-full">
                                <label for="is_cash_payout" class="theme-input-label pt-0">
                                    {{ translate('Cash Payment') }}
                                </label>
                                <div class="theme-input-wrapper flex gap-3">
                                    <x-backend.inputs.checkbox toggler="true" name="is_cash_payout" value="1"
                                        isChecked="{{ $shop->is_cash_payout }}" />
                                </div>
                            </div>

                            <div class="theme-input-group w-full">
                                <label for="is_bank_payout" class="theme-input-label pt-0">
                                    {{ translate('Bank Payment') }}
                                </label>
                                <div class="theme-input-wrapper flex gap-3">
                                    <x-backend.inputs.checkbox toggler="true" name="is_bank_payout" value="1"
                                        isChecked="{{ $shop->is_bank_payout }}" />
                                </div>
                            </div>


                            <x-backend.inputs.text label="Bank Name" name="bank_name" placeholder="Type bank name"
                                value="{{ $shop->bank_name }}" placeholder="Stripe" :isRequired="false" />


                            <x-backend.inputs.text label="Bank Account Name" name="bank_acc_name"
                                placeholder="Type bank name" value="{{ $shop->bank_acc_name }}" placeholder="Mr. Epik"
                                :isRequired="false" />


                            <x-backend.inputs.text label="Bank Account No." name="bank_acc_no"
                                placeholder="Type bank account number" value="{{ $shop->bank_acc_no }}"
                                placeholder="4242424242424242" :isRequired="false" />


                            <x-backend.inputs.text label="Bank Routing No." name="bank_routing_no"
                                placeholder="Type bank routing number" value="{{ $shop->bank_routing_no }}"
                                placeholder="332" :isRequired="false" />

                            <div class="flex justify-end">
                                <x-backend.inputs.button buttonText="Update Settings" type="submit" :isRequired="false" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end::product-form -->
@endsection

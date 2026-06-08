@extends('layouts.admin')

@section('title')
    {{ translate('Payment method settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Payment Methods') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Payment Settings') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        {{-- cod --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Cash On Delivery') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="cod">

                    <x-backend.inputs.checkbox toggler="true" label="COD Activation" name="cod_activation" value="1"
                        isChecked="{{ getSetting('cod_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- card --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Card On Delivery') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="card">

                    <x-backend.inputs.checkbox toggler="true" label="Card Activation" name="card_activation" value="1"
                        isChecked="{{ getSetting('card_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- paypal --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Paypal Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="paypal">

                    <input type="hidden" name="types[]" value="PAYPAL_CLIENT_ID">
                    <x-backend.inputs.text label="Paypal Client Id" type="text" name="PAYPAL_CLIENT_ID"
                        value="{{ config('app.PAYPAL_CLIENT_ID') }}" placeholder="Paypal Client ID" />

                    <input type="hidden" name="types[]" value="PAYPAL_CLIENT_SECRET">
                    <x-backend.inputs.text label="Paypal Client Secret" name="PAYPAL_CLIENT_SECRET"
                        value="{{ config('app.PAYPAL_CLIENT_SECRET') }}" placeholder="Paypal Client Secret" />

                    <x-backend.inputs.checkbox toggler="true" label="Paypal Sandbox Mode" name="paypal_sandbox"
                        value="1" isChecked="{{ getSetting('paypal_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="Paypal Activation" name="paypal_activation"
                        value="1" isChecked="{{ getSetting('paypal_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- stripe --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Stripe Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf

                    <input type="hidden" name="payment_method" value="stripe">

                    <input type="hidden" name="types[]" value="STRIPE_KEY">
                    <x-backend.inputs.text label="Stripe Key" type="text" name="STRIPE_KEY"
                        value="{{ config('app.STRIPE_KEY') }}" placeholder="Stripe Key" />

                    <input type="hidden" name="types[]" value="STRIPE_SECRET">
                    <x-backend.inputs.text label="Stripe Secret" name="STRIPE_SECRET"
                        value="{{ config('app.STRIPE_SECRET') }}" placeholder="Stripe Secret" />

                    <x-backend.inputs.checkbox toggler="true" label="STRIPE Activation" name="stripe_activation"
                        value="1" isChecked="{{ getSetting('stripe_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- flutterwave --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Flutterwave Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="flutterwave">

                    <input type="hidden" name="types[]" value="FLW_PUBLIC_KEY">
                    <x-backend.inputs.text label="Flutterwave Public Key" name="FLW_PUBLIC_KEY"
                        value="{{ config('app.FLW_PUBLIC_KEY') }}" placeholder="Flutterwave Public Key" />

                    <input type="hidden" name="types[]" value="FLW_SECRET_KEY">
                    <x-backend.inputs.text label="Flutterwave Secret Key" name="FLW_SECRET_KEY"
                        value="{{ config('app.FLW_SECRET_KEY') }}" placeholder="Flutterwave Secret Key" />

                    <input type="hidden" name="types[]" value="FLW_SECRET_HASH">
                    <x-backend.inputs.text label="Flutterwave Encryption Key" name="FLW_SECRET_HASH"
                        value="{{ config('app.FLW_SECRET_HASH') }}" placeholder="Flutterwave Encryption Key" />

                    <x-backend.inputs.checkbox toggler="true" label="Flutterwave Activation" value=""
                        name="flutterwave_activation" isChecked="{{ getSetting('flutterwave_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- PayTm Credentials --}}
        <div class="card">
            <h4 class="card__title">{{ translate('PayTm Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="paytm">

                    <input type="hidden" name="types[]" value="PAYTM_ENVIRONMENT">
                    <x-backend.inputs.text label="PayTm Environment" name="PAYTM_ENVIRONMENT"
                        value="{{ config('app.PAYTM_ENVIRONMENT') }}" placeholder="PayTm Environment" />

                    <input type="hidden" name="types[]" value="PAYTM_MERCHANT_ID">
                    <x-backend.inputs.text label="PayTm Merchant ID" name="PAYTM_MERCHANT_ID"
                        value="{{ config('app.PAYTM_MERCHANT_ID') }}" placeholder="PayTm Merchant ID" />

                    <input type="hidden" name="types[]" value="PAYTM_MERCHANT_KEY">
                    <x-backend.inputs.text label="PayTm Merchant Key" name="PAYTM_MERCHANT_KEY"
                        value="{{ config('app.PAYTM_MERCHANT_KEY') }}" placeholder="PayTm Merchant Key" />

                    <input type="hidden" name="types[]" value="PAYTM_MERCHANT_WEBSITE">
                    <x-backend.inputs.text label="PayTm Merchant Website" name="PAYTM_MERCHANT_WEBSITE"
                        value="{{ config('app.PAYTM_MERCHANT_WEBSITE') }}" placeholder="PayTm Merchant Website" />

                    <input type="hidden" name="types[]" value="PAYTM_CHANNEL">
                    <x-backend.inputs.text label="PayTm Channel" name="PAYTM_CHANNEL"
                        value="{{ config('app.PAYTM_CHANNEL') }}" placeholder="PayTm Channel" />

                    <input type="hidden" name="types[]" value="PAYTM_INDUSTRY_TYPE">
                    <x-backend.inputs.text label="PayTm Industry Type" name="PAYTM_INDUSTRY_TYPE"
                        value="{{ config('app.PAYTM_INDUSTRY_TYPE') }}" placeholder="PayTm Industry Type" />

                    <x-backend.inputs.checkbox toggler="true" label="PayTm Activation" value=""
                        name="paytm_activation" isChecked="{{ getSetting('paytm_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- paystack --}}
        <div class="card">
            <h4 class="card__title">{{ translate('PayStack Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="payment_method" value="paystack">

                    <input type="hidden" name="types[]" value="PAYSTACK_PUBLIC_KEY">
                    <x-backend.inputs.text label="PUBLIC KEY" type="text" name="PAYSTACK_PUBLIC_KEY"
                        value="{{ config('app.paystack_public_key') }}" placeholder="PUBLIC KEY" />

                    <input type="hidden" name="types[]" value="PAYSTACK_SECRET_KEY">
                    <x-backend.inputs.text label="SECRET KEY" name="PAYSTACK_SECRET_KEY"
                        value="{{ config('app.paystack_secret_key') }}" placeholder="SECRET KEY" />

                    <input type="hidden" name="types[]" value="PAYSTACK_MERCHANT_EMAIL">
                    <x-backend.inputs.text label="MERCHANT EMAIL" name="PAYSTACK_MERCHANT_EMAIL"
                        value="{{ config('app.paystack_merchant_email') }}" placeholder="MERCHANT EMAIL" />

                    <input type="hidden" name="types[]" value="PAYSTACK_CURRENCY_CODE">
                    <x-backend.inputs.text label="PAYSTACK CURRENCY CODE" name="PAYSTACK_CURRENCY_CODE"
                        value="{{ config('app.paystack_currency_code') }}" placeholder="PAYSTACK CURRENCY CODE" />

                    <x-backend.inputs.checkbox toggler="true" label="PAYSTACK Activation" name="paystack_activation"
                        value="1" isChecked="{{ getSetting('paystack_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="Save Changes" />
                    </div>
                </form>
            </div>
        </div>

        {{-- sslcommerz --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Sslcommerz Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="sslcommerz">

                    <input type="hidden" name="types[]" value="SSLCZ_STORE_ID">
                    <x-backend.inputs.text label="Sslcz Store Id" name="SSLCZ_STORE_ID"
                        value="{{ config('app.SSLCZ_STORE_ID') }}" placeholder="Sslcz Store Id" />

                    <input type="hidden" name="types[]" value="SSLCZ_STORE_PASSWD">
                    <x-backend.inputs.text label="Sslcz store password" name="SSLCZ_STORE_PASSWD"
                        value="{{ config('app.SSLCZ_STORE_PASSWD') }}" placeholder="Sslcz store password" />

                    <x-backend.inputs.checkbox toggler="true" label="Sslcommerz Sandbox Mode" value=""
                        name="sslcommerz_sandbox" isChecked="{{ getSetting('sslcommerz_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="Sslcommerz Activation" value=""
                        name="sslcommerz_activation" isChecked="{{ getSetting('sslcommerz_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        
        {{-- bkash --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Bkash Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="bkash">

                    <input type="hidden" name="types[]" value="BKASH_CHECKOUT_APP_KEY">
                    <x-backend.inputs.text label="BKASH CHECKOUT APP KEY" name="BKASH_CHECKOUT_APP_KEY"
                        value="{{ config('app.BKASH_CHECKOUT_APP_KEY') }}" placeholder="BKASH CHECKOUT APP KEY" />

                    <input type="hidden" name="types[]" value="BKASH_CHECKOUT_APP_SECRET">
                    <x-backend.inputs.text label="BKASH CHECKOUT APP SECRET" name="BKASH_CHECKOUT_APP_SECRET"
                        value="{{ config('app.BKASH_CHECKOUT_APP_SECRET') }}" placeholder="BKASH CHECKOUT APP SECRET" />

                    <input type="hidden" name="types[]" value="BKASH_CHECKOUT_USER_NAME">
                    <x-backend.inputs.text label="BKASH CHECKOUT USER NAME" name="BKASH_CHECKOUT_USER_NAME"
                        value="{{ config('app.BKASH_CHECKOUT_USER_NAME') }}" placeholder="BKASH CHECKOUT USER NAME" />

                    <input type="hidden" name="types[]" value="BKASH_CHECKOUT_PASSWORD">
                    <x-backend.inputs.text label="BKASH CHECKOUT PASSWORD" name="BKASH_CHECKOUT_PASSWORD"
                        value="{{ config('app.BKASH_CHECKOUT_PASSWORD') }}" placeholder="BKASH CHECKOUT PASSWORD" />

                    <x-backend.inputs.checkbox toggler="true" label="Bkash Sandbox Mode" value=""
                        name="bkash_sandbox" isChecked="{{ getSetting('bkash_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="Bkash Activation" value=""
                        name="bkash_activation" isChecked="{{ getSetting('bkash_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        
        {{-- CoinGate --}}
        <div class="card">
            <h4 class="card__title">{{ translate('CoinGate Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf

                    <input type="hidden" name="payment_method" value="coingate">

                    <input type="hidden" name="types[]" value="COINGATE_API_KEY">
                    <x-backend.inputs.text label="COINGATE API KEY" type="text" name="COINGATE_API_KEY"
                        value="{{ config('app.COINGATE_API_KEY') }}" placeholder="COINGATE API KEY" />

                    <x-backend.inputs.checkbox toggler="true" label="CoinGate Sandbox Mode" name="coingate_sandbox"
                        value="1" isChecked="{{ getSetting('coingate_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="CoinGate Activation" name="coingate_activation"
                        value="1" isChecked="{{ getSetting('coingate_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- iyzico --}}
        <div class="card">
            <h4 class="card__title">{{ translate('IyZico Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="iyzico">

                    <input type="hidden" name="types[]" value="IYZICO_API_KEY">
                    <x-backend.inputs.text label="IyZico API Key" name="IYZICO_API_KEY"
                        value="{{ config('app.IYZICO_API_KEY') }}" placeholder="IyZico API Key" />

                    <input type="hidden" name="types[]" value="IYZICO_SECRET_KEY">
                    <x-backend.inputs.text label="IyZico Secret Key" name="IYZICO_SECRET_KEY"
                        value="{{ config('app.IYZICO_SECRET_KEY') }}" placeholder="IyZico Secret Key" />

                    <x-backend.inputs.checkbox toggler="true" label="IyZico Sandbox Mode" value=""
                        name="iyzico_sandbox" isChecked="{{ getSetting('iyzico_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="IyZico Activation" value=""
                        name="iyzico_activation" isChecked="{{ getSetting('iyzico_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- razorpay --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Razorpay Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="razorpay">

                    <input type="hidden" name="types[]" value="RAZORPAY_KEY">
                    <x-backend.inputs.text label="Razorpay Key" name="RAZORPAY_KEY"
                        value="{{ config('app.RAZORPAY_KEY') }}" placeholder="Razorpay Key" />


                    <input type="hidden" name="types[]" value="RAZORPAY_SECRET">
                    <x-backend.inputs.text label="Razorpay Secret" name="RAZORPAY_SECRET"
                        value="{{ config('app.RAZORPAY_SECRET') }}" placeholder="Razorpay Secret" />

                    <x-backend.inputs.checkbox toggler="true" label="Razorpay Activation" value=""
                        name="razorpay_activation" isChecked="{{ getSetting('razorpay_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- Instamojo --}}
        <div class="card">
            <h4 class="card__title">{{ translate('Instamojo Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf

                    <input type="hidden" name="payment_method" value="instamojo">

                    <input type="hidden" name="types[]" value="IM_API_KEY">
                    <x-backend.inputs.text label="API KEY" type="text" name="IM_API_KEY"
                        value="{{ config('app.IM_API_KEY') }}" placeholder="IM API KEY" />

                    <input type="hidden" name="types[]" value="IM_AUTH_TOKEN">
                    <x-backend.inputs.text label="AUTH TOKEN" name="IM_AUTH_TOKEN"
                        value="{{ config('app.IM_AUTH_TOKEN') }}" placeholder="IM AUTH TOKEN" />

                    <x-backend.inputs.checkbox toggler="true" label="Instamojo Sandbox" name="instamojo_sandbox"
                        value="1" isChecked="{{ getSetting('instamojo_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="Instamojo Activation" name="instamojo_activation"
                        value="1" isChecked="{{ getSetting('instamojo_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

        {{-- aamarpay --}}
        <div class="card hidden">
            <h4 class="card__title">{{ translate('Aamarpay Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.payment-method.update') }}" method="post" class="space-y-3">
                    @csrf

                    <input type="hidden" name="payment_method" value="aamarpay">

                    <input type="hidden" name="types[]" value="AAMARPAY_STORE_ID">
                    <x-backend.inputs.text label="Aamarpay Store Id" name="AAMARPAY_STORE_ID"
                        value="{{ config('app.AAMARPAY_STORE_ID') }}" placeholder="Aamarpay Store Id" />

                    <input type="hidden" name="types[]" value="AAMARPAY_SIGNATURE_KEY">
                    <x-backend.inputs.text label="Aamarpay Signature Key" name="AAMARPAY_SIGNATURE_KEY"
                        value="{{ config('app.AAMARPAY_SIGNATURE_KEY') }}" placeholder="Aamarpay signature key" />

                    <x-backend.inputs.checkbox toggler="true" label="Aamarpay Sandbox Mode" name="aamarpay_sandbox"
                        value="1" isChecked="{{ getSetting('aamarpay_sandbox') == 1 }}" />

                    <x-backend.inputs.checkbox toggler="true" label="Aamarpay Activation" name="aamarpay_activation"
                        value="1" isChecked="{{ getSetting('aamarpay_activation') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

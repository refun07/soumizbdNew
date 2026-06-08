@extends('layouts.auth')

@section('title')
    {{ translate('Reset Password') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="min-h-screen flex max-lg:flex-col bg-white">
        <div class="w-full lg:max-w-[600px] mx-auto py-8 2xl:py-12 h-screen overflow-y-auto">
            <div class="max-w-[450px] px-[15px] h-full mx-auto flex flex-col gap-8 xl:gap-14 justify-between">
                <div>
                    <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}" alt="">
                </div>

                <div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <h1 class="mb-8 text-xl md:text-[26px] font-semibold">{{ translate('Reset Password') }}</h1>

                        <div>
                            <label class="mb-1 uppercase text-[11px]">{{ translate('Email') }}</label>
                            <x-backend.inputs.text name="email" id="email" placeholder="{{ translate('Email') }}" />
                        </div>

                        <div class="mt-5">
                            <x-backend.inputs.button type="submit"
                                class="w-full justify-center !bg-[#005BFF]">{{ translate('Send Password Reset Link') }}</x-backend.inputs.button>
                        </div>
                    </form>
                </div>

                <div>
                    {{ translate('Back to') }}
                    <a href="{{ route('login') }}" class="text-secondary-light">{{ translate('Login') }}</a>
                </div>
            </div>
        </div>
        <div class="grow p-8 relative hidden lg:flex flex-col justify-center items-center text-white bg-cover"
            style="background-image: url({{ asset('images/auth-bg.png') }})">
            <div class="w-full max-w-[513px]">
                <h2 class="text-2xl xl:text-[44px] font-normal mb-5">{{ translate('Reset Password') }}</h2>
                <p class="text-lg">
                    {{ translate('Reset your password.') }}
                </p>
            </div>
        </div>
    </div>
@endsection

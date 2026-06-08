@extends('layouts.auth')

@section('title')
    {{ translate('Login') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="min-h-screen flex max-lg:flex-col bg-white">
        <div class="w-full lg:max-w-[600px] mx-auto py-8 2xl:py-12 h-screen overflow-y-auto">
            <div class="max-w-[450px] px-[15px] h-full mx-auto flex flex-col gap-8 xl:gap-14 justify-between">
                <div>
                    <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}" alt="">
                </div>

                <div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <h1 class="mb-8 text-xl md:text-[26px] font-semibold">{{ translate('Login to Dashboard') }}</h1>

                        <div>
                            <label class="mb-1 uppercase text-[11px]">{{ translate('Email') }}</label>
                            <x-backend.inputs.text name="email" id="email" placeholder="{{ translate('Email') }}" />
                        </div>
                        <div class="mt-4">
                            <label class="mb-1 uppercase text-[11px]">{{ translate('Password') }}</label>
                            <x-backend.inputs.password name="password" id="password"
                                placeholder="{{ translate('Password') }}" />

                            <label class="flex items-center gap-2 mt-2 text-xs">
                                <x-backend.inputs.checkbox name='toggle-password' class="toggle-password"
                                    data-target="#password" />
                                <span>{{ translate('Show Password') }}</span>
                            </label>
                        </div>

                        <div class="mt-5">
                            <x-backend.inputs.button type="submit"
                                class="w-full justify-center !bg-[#005BFF]">{{ translate('Login') }}</x-backend.inputs.button>
                        </div>

                        <label class="mt-6 flex gap-3 items-center text-xs">
                            <x-backend.inputs.checkbox name="remember_me" />
                            <span>{{ translate('Remember Me') }}</span>
                        </label>
                    </form>

                    @if (config('app.demo_mode') == 'On')
                        <div class="mt-12 space-y-4">
                            <div
                                class="flex bg-[#F1F1F1] border border-[#E3E3E3] text-neutral-400 rounded-md overflow-hidden max-w-[420px]">
                                <input name="email" class="w-full text-xs px-4 py-3 bg-transparent"
                                    value="admin@epikcart.com" readonly />
                                <input name="password"
                                    class="w-full text-xs px-2 py-3 bg-transparent border-l border-[#E3E3E3]" value="123456"
                                    readonly />
                                <button
                                    class="copy-password text-xs px-4 py-3 bg-secondary-light text-white font-bold">COPY</button>
                            </div>
                            <div
                                class="flex bg-[#F1F1F1] border border-[#E3E3E3] text-neutral-400 rounded-md overflow-hidden max-w-[420px]">
                                <input name="email" class="w-full text-xs px-4 py-3 bg-transparent"
                                    value="seller@epikcart.com" readonly />
                                <input name="password"
                                    class="w-full text-xs px-2 py-3 bg-transparent border-l border-[#E3E3E3]" value="123456"
                                    readonly />
                                <button
                                    class="copy-password text-xs px-4 py-3 bg-secondary-light text-white font-bold">COPY</button>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    {{ translate('Forgot The Pasword?') }}
                    <a href="{{ route('password.request') }}"
                        class="text-secondary-light">{{ translate('Recover Now') }}</a>
                </div>
            </div>
        </div>
        <div class="grow p-8 relative hidden lg:flex flex-col justify-center items-center text-white bg-cover"
            style="background-image: url({{ asset('images/auth-bg.png') }})">
            <div class="w-full max-w-[513px]">
                <h2 class="text-2xl xl:text-[44px] font-normal mb-5">{{ translate('Welcome Back Admin!') }}</h2>
                <p class="text-lg">
                    {{ translate('Login to get all your latest order updates, notifications, edit options and activity log.') }}
                </p>
            </div>
        </div>
    </div>

    <script>
        "use strict";
        // insert email and password in the inputs when copy button is clicked
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const copyBtn = document.querySelectorAll('.copy-password');

        copyBtn?.forEach(btn => {
            btn.addEventListener('click', () => {
                const email = btn.parentElement.querySelector('input[name="email"]').value;
                const password = btn.parentElement.querySelector('input[name="password"]').value;

                emailInput.value = email;
                passwordInput.value = password;

                btn.innerHTML = 'COPIED';
                setTimeout(() => {
                    btn.innerHTML = 'COPY';
                }, 5000);
            });
        });

        // toggle password visibility
        const togglePassword = document.querySelector('.toggle-password');
        const target = togglePassword.getAttribute('data-target');
        const passwordInputEl = document.querySelector(target);

        togglePassword.addEventListener('change', () => {

            if (passwordInputEl.type === 'password') {
                passwordInputEl.type = 'text';
            } else {
                passwordInputEl.type = 'password';
            }
        });
    </script>
@endsection

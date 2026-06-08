@extends('layouts.admin')

@section('title')
    {{ translate('SMTP settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('SMTP Settings') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Configurations') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('SMTP Settings') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card">
                <div class="card__content">
                    <form action="{{ route('admin.env-key.update') }}" method="POST">
                        <div class="space-y-3">
                            @csrf

                            <input type="hidden" name="types[]" value="MAIL_MAILER">
                            <x-backend.inputs.select label="Type" name="MAIL_MAILER" onchange="checkMailDriver()">
                                <x-backend.inputs.select-option name="SMTP" value="smtp"
                                    selected="{{ config('app.mail_mailer') == 'smtp' ? 'smtp' : '' }}" />
                                <x-backend.inputs.select-option name="Mailgun" value="mailgun"
                                    selected="{{ config('app.mail_mailer') == 'mailgun' ? 'mailgun' : '' }}" />
                                <x-backend.inputs.select-option name="SendMail" value="sendmail"
                                    selected="{{ config('app.mail_mailer') == 'sendmail' ? 'sendmail' : '' }}" />
                            </x-backend.inputs.select>


                            <div id="smtp" class="space-y-3">

                                <input type="hidden" name="types[]" value="MAIL_HOST">
                                <x-backend.inputs.text label="Host" name="MAIL_HOST" placeholder="Host"
                                    value="{{ config('app.mail_host') }}" :isRequired="false" />


                                <input type="hidden" name="types[]" value="MAIL_PORT">
                                <x-backend.inputs.text label="Port" name="MAIL_PORT" placeholder="Port"
                                    value="{{ config('app.mail_port') }}" :isRequired="false" />


                                <input type="hidden" name="types[]" value="MAIL_USERNAME">
                                <x-backend.inputs.text label="Username" name="MAIL_USERNAME" placeholder="Username"
                                    value="{{ config('app.mail_username') }}" :isRequired="false" />


                                <input type="hidden" name="types[]" value="MAIL_PASSWORD">
                                <x-backend.inputs.text label="Password" name="MAIL_PASSWORD" placeholder="Password"
                                    value="{{ config('app.mail_password') }}" :isRequired="false" />

                                <input type="hidden" name="types[]" value="MAIL_ENCRYPTION">
                                <x-backend.inputs.text label="Encryption" name="MAIL_ENCRYPTION" placeholder="Encryption"
                                    value="{{ config('app.mail_encryption') }}" :isRequired="false" />


                                <input type="hidden" name="types[]" value="MAIL_FROM_ADDRESS">
                                <x-backend.inputs.text label="From Address" name="MAIL_FROM_ADDRESS"
                                    placeholder="From Address" value="{{ config('app.mail_from_address') }}"
                                    :isRequired="false" />


                                <input type="hidden" name="types[]" value="MAIL_FROM_NAME">
                                <x-backend.inputs.text label="From Name" name="MAIL_FROM_NAME" placeholder="From Name"
                                    value="{{ config('app.mail_from_name') }}" :isRequired="false" />
                            </div>


                            <div id="mailgun" class="space-y-3">

                                <input type="hidden" name="types[]" value="MAILGUN_DOMAIN">
                                <x-backend.inputs.text label="Domain" name="MAILGUN_DOMAIN" placeholder="Domain"
                                    value="{{ config('app.mailgun_domain') }}" :isRequired="false" />

                                <input type="hidden" name="types[]" value="MAILGUN_SECRET">
                                <x-backend.inputs.text label="Secret" name="MAILGUN_SECRET" placeholder="Secret"
                                    value="{{ config('app.mailgun_secret') }}" :isRequired="false" />
                            </div>



                            <div class="flex justify-end">
                                <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            <div class="card">
                <h4 class="card__title">{{ translate('Test SMTP configuration') }}</h4>
                <div class="card__content">
                    <form action="{{ route('test.smtp') }}" method="post" class="theme-input-wrapper space-y-4">
                        @csrf
                        <div class="grow">
                            <input type="text" name="email" class="theme-input" placeholder="email@domain.com" />
                        </div>
                        <div class="flex justify-end">
                            <x-backend.inputs.button type="submit" buttonText="{{ translate('Test Connection') }}" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            checkMailDriver();
        });

        function checkMailDriver() {
            if ($('select[name=MAIL_MAILER]').val() == 'mailgun') {
                $('#mailgun').show();
                $('#smtp').hide();
            } else {
                $('#mailgun').hide();
                $('#smtp').show();
            }
        }
    </script>
@endsection

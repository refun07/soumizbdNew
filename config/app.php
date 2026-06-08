<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Custom Configuration
    |--------------------------------------------------------------------------
    |
    */
    'app_mode'      => env('APP_MODE', 'multiVendor'),
    'api_version'   => env('APP_VERSION', 'v1'),
    'api_pathname'  => env('APP_PATHNAME', '/api/v1'),
    'demo_mode'     => env('DEMO_MODE', 'Off'),
    'force_https'   => env('FORCE_HTTPS', false),

    // Language
    'default_language'  => env('DEFAULT_LANGUAGE', 'en-US'),

    // Mail
    'mail_mailer'       => env('MAIL_MAILER'),
    'mail_host'         => env('MAIL_HOST'),
    'mail_port'         => env('MAIL_PORT'),
    'mail_username'     => env('MAIL_USERNAME'),
    'mail_password'     => env('MAIL_PASSWORD'),
    'mail_encryption'   => env('MAIL_ENCRYPTION'),
    'mail_from_address' => env('MAIL_FROM_ADDRESS'),
    'mail_from_name'    => env('MAIL_FROM_NAME'),
    'mailgun_domain'    => env('MAILGUN_DOMAIN'),
    'mailgun_secret'    => env('MAILGUN_SECRET'),

    // payment gateways
    'PAYPAL_CLIENT_ID'          => env('PAYPAL_CLIENT_ID', ''),
    'PAYPAL_CLIENT_SECRET'      => env('PAYPAL_CLIENT_SECRET', ''),

    'STRIPE_KEY'                => env('STRIPE_KEY', ''),
    'STRIPE_SECRET'             => env('STRIPE_SECRET', ''),

    'FLW_PUBLIC_KEY'            => env('FLW_PUBLIC_KEY', ''),
    'FLW_SECRET_KEY'            => env('FLW_SECRET_KEY', ''),
    'FLW_SECRET_HASH'           => env('FLW_SECRET_HASH', ''),

    'PAYTM_ENVIRONMENT'         => env('PAYTM_ENVIRONMENT', ''),
    'PAYTM_MERCHANT_ID'         => env('PAYTM_MERCHANT_ID', ''),
    'PAYTM_MERCHANT_KEY'        => env('PAYTM_MERCHANT_KEY', ''),
    'PAYTM_MERCHANT_WEBSITE'    => env('PAYTM_MERCHANT_WEBSITE', ''),
    'PAYTM_CHANNEL'             => env('PAYTM_CHANNEL', ''),
    'PAYTM_INDUSTRY_TYPE'       => env('PAYTM_INDUSTRY_TYPE', ''),

    'paystack_public_key'       => env('PAYSTACK_PUBLIC_KEY', ''),
    'paystack_secret_key'       => env('PAYSTACK_SECRET_KEY', ''),
    'paystack_merchant_email'   => env('PAYSTACK_MERCHANT_EMAIL', ''),
    'paystack_currency_code'    => env('PAYSTACK_CURRENCY_CODE', 'usd'),
    'paystack_activation'       => env('INSTAMOJO_ACTIVATION', 0),

    'SSLCZ_STORE_ID'            => env('SSLCZ_STORE_ID', ''),
    'SSLCZ_STORE_PASSWD'        => env('SSLCZ_STORE_PASSWD', ''),

    'BKASH_CHECKOUT_APP_KEY'    => env('BKASH_CHECKOUT_APP_KEY', ''),
    'BKASH_CHECKOUT_APP_SECRET' => env('BKASH_CHECKOUT_APP_SECRET', ''),
    'BKASH_CHECKOUT_USER_NAME'  => env('BKASH_CHECKOUT_USER_NAME', ''),
    'BKASH_CHECKOUT_PASSWORD'   => env('BKASH_CHECKOUT_PASSWORD', ''),

    'COINGATE_API_KEY'          => env('COINGATE_API_KEY', ''),

    'IYZICO_API_KEY'            => env('IYZICO_API_KEY', ''),
    'IYZICO_SECRET_KEY'         => env('IYZICO_SECRET_KEY', ''),

    'RAZORPAY_KEY'              => env('RAZORPAY_KEY', ''),
    'RAZORPAY_SECRET'           => env('RAZORPAY_SECRET', ''),

    'AAMARPAY_STORE_ID'         => env('AAMARPAY_STORE_ID', ''),
    'AAMARPAY_SIGNATURE_KEY'    => env('AAMARPAY_SIGNATURE_KEY', ''),

    'IM_API_KEY'                => env('IM_API_KEY', ''),
    'IM_AUTH_TOKEN'             => env('IM_AUTH_TOKEN', ''),

    // social logins
    'GOOGLE_CLIENT_ID'          => env('GOOGLE_CLIENT_ID', ''),
    'GOOGLE_CLIENT_SECRET'      => env('GOOGLE_CLIENT_SECRET', ''),
    'GOOGLE_ACTIVATION'         => env('GOOGLE_ACTIVATION', '0'),
    'GOOGLE_ANALYTICS_ACTIVATION' => env('GOOGLE_ANALYTICS_ACTIVATION', '0'),

    'FACEBOOK_CLIENT_ID'        => env('FACEBOOK_CLIENT_ID', ''),
    'FACEBOOK_CLIENT_SECRET'    => env('FACEBOOK_CLIENT_SECRET', ''),
    'FACEBOOK_ACTIVATION'       => env('FACEBOOK_ACTIVATION', ''),
    'FACEBOOK_PIXEL_ACTIVATION' => env('FACEBOOK_PIXEL_ACTIVATION', '0'),

    'OTP_ACTIVATION' => env('OTP_ACTIVATION', '0'),




    'META_PIXEL_ID'        => env('META_PIXEL_ID', ''),
    'META_CONVERSIONS_API_ACCESS_TOKEN'    => env('META_CONVERSIONS_API_ACCESS_TOKEN', ''),
    'FACEBOOK_AD_ACCOUNT_ID'       => env('FACEBOOK_AD_ACCOUNT_ID', ''),
    'FB_TEST_EVENT_CODE'       => env('FB_TEST_EVENT_CODE', ''),

    'GTM_ID'        => env('GTM_ID', ''),
    'GA4_API_SECRET'    => env('GA4_API_SECRET', ''),
    'GA4_MEASUREMENT_ID'       => env('GA4_MEASUREMENT_ID', ''),


    'SMS_API_URL'    => env('SMS_API_URL', ''),
    'SMS_API_KEY'       => env('SMS_API_KEY', ''),
    'SMS_SENDER_ID'       => env('SMS_SENDER_ID', ''),


    'STEADFAST_API_KEY' => env('STEADFAST_API_KEY', ''),
    'STEADFAST_SECRET_KEY' => env('STEADFAST_SECRET_KEY', ''),
    'STEADFAST_BASE_URL' => env('STEADFAST_BASE_URL', ''),
    'STEADFAST_ACTIVATION' => env('STEADFAST_ACTIVATION', ''),


    'REDX_API_KEY' => env('REDX_API_KEY', ''),
    'REDX_BASE_URL' => env('REDX_BASE_URL', ''),
    'REDX_ACTIVATION' => env('REDX_ACTIVATION', ''),


    'PATHAO_CLIENT_ID' => env('PATHAO_CLIENT_ID', ''),
    'PATHAO_CLIENT_SECRET' => env('PATHAO_CLIENT_SECRET', ''),
    'PATHAO_USERNAME' => env('PATHAO_USERNAME', ''),
    'PATHAO_PASSWORD' => env('PATHAO_PASSWORD', ''),
    'PATHAO_BASE_URL' => env('PATHAO_BASE_URL', ''),
    'PATHAO_ACTIVATION' => env('PATHAO_ACTIVATION', ''),

    // Fraud Checker API Credentials
    'FRAUDCHECK_STEADFAST_USER'     => env('FRAUDCHECK_STEADFAST_USER', ''),
    'FRAUDCHECK_STEADFAST_PASSWORD' => env('FRAUDCHECK_STEADFAST_PASSWORD', ''),
    'FRAUDCHECK_PATHAO_USER'        => env('FRAUDCHECK_PATHAO_USER', ''),
    'FRAUDCHECK_PATHAO_PASSWORD'    => env('FRAUDCHECK_PATHAO_PASSWORD', ''),
    'FRAUDCHECK_REDX_PHONE'         => env('FRAUDCHECK_REDX_PHONE', ''),
    'FRAUDCHECK_REDX_PASSWORD'      => env('FRAUDCHECK_REDX_PASSWORD', ''),

    'WHATSAPP_BASE_URL' => env('WHATSAPP_BASE_URL', ''),
    'WHATSAPP_API_KEY' => env('WHATSAPP_API_KEY', ''),


    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', "UTC"),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('DEFAULT_LANGUAGE', 'en-US'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en-US',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        // custom
        App\Providers\Custom\OrderServiceProvider::class,
    ])->toArray(),

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),

];

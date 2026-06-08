<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    # paytm
    'paytm-wallet' => [
        'env'               => env('PAYTM_ENVIRONMENT'), // values : (local | production)
        'merchant_id'       => env('PAYTM_MERCHANT_ID'),
        'merchant_key'      => env('PAYTM_MERCHANT_KEY'),
        'merchant_website'  => env('PAYTM_MERCHANT_WEBSITE'),
        'channel'           => env('PAYTM_CHANNEL'),
        'industry_type'     => env('PAYTM_INDUSTRY_TYPE'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/social-login/google/callback',
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('APP_URL') . '/social-login/facebook/callback',
    ],
    'gtm_id' => env('GTM_ID', null),

    'ga4' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID'),
        'api_secret' => env('GA4_API_SECRET'),
    ],
    // config/services.php
    'sms' => [
        'api_url' => env('SMS_API_URL', 'https://bulksmsbd.net/api/smsapi'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
    ],
    'steadfast' => [
        'api_key' => env('STEADFAST_API_KEY'),
        'secret_key' => env('STEADFAST_SECRET_KEY'),
        'base_url'=>env('STEADFAST_BASE_URL')
    ],
    'redx' => [
        'api_key' => env('REDX_API_KEY'),
        'base_url'=>env('REDX_BASE_URL')
    ],
    'pathao' => [
        'client_id' => env('PATHAO_CLIENT_ID'),
        'client_secret' => env('PATHAO_CLIENT_SECRET'),
        'username' => env('PATHAO_USERNAME'),
        'password' => env('PATHAO_PASSWORD'),
        'base_url' => env('PATHAO_BASE_URL'),
    ],

    'fraudbd' => [
        'api_key' => env('FRAUD_BD_API_KEY'),
        'username' => env('FRAUD_BD_USERNAME'),
        'password' => env('FRAUD_BD_PASSWORD'),
    ],

    'whatsapp' => [
        'app_id' => env('WHATSAPP_APP_ID'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'chat_activation' => env('WHATSAPP_CHAT_ACTIVATION'),
    ],

    'messenger' => [
        'app_id' => env('MESSENGER_APP_ID'),
        'app_secret' => env('MESSENGER_APP_SECRET'),
        'page_access_token' => env('MESSENGER_PAGE_ACCESS_TOKEN'),
        'verify_token' => env('MESSENGER_VERIFY_TOKEN'),
        'chat_activation' => env('MESSENGER_CHAT_ACTIVATION'),
    ],

];

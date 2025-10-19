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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'github' => [
        'token' => env('GITHUB_TOKEN'),
    ],

    'images' => [
        'services' => [
            'service-1' => 'images/services/service-1.jpg',
            'service-2' => 'images/services/service-2.jpg',
            'service-3' => 'images/services/service-3.jpg',
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'revenuecat' => [
        'api_key' => env('REVENUECAT_API_KEY'),
        'webhook_secret' => env('REVENUECAT_WEBHOOK_SECRET'),
        'public_app_key' => env('REVENUECAT_PUBLIC_APP_KEY'),
        'ios_bundle_id' => env('IOS_BUNDLE_ID', 'com.yourcompany.karma'),
        'android_package' => env('ANDROID_PACKAGE', 'com.yourcompany.karma'),
    ],

];

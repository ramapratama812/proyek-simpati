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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // setting untuk PDDIKTI (belum digunakan)
    'pddikti'=>[
        'base'=>env('PDDIKTI_BASE','http://localhost:3003'),
        'username'=>env('PDDIKTI_USER'),
        'password'=>env('PDDIKTI_PASS'),
    ],

    // setting untuk google oauth dan drive
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        // NOTE: jangan dipakai mentah kalau callback login dan callback drive beda.
        // Nanti di controller Drive kita override pakai redirectUrl().
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    // setting untuk SINTA
    'sinta' => [
        'base_url' => env('SINTA_BASE_URL', 'https://sinta.kemdiktisaintek.go.id'),
        'timeout'  => env('SINTA_TIMEOUT', 10),
    ],

];

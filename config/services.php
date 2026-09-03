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

    'team_api' => [
        'base_url' => env('TEAM_API_BASE_URL'),
        'id' => env('TEAM_API_ID'),
        'secret' => env('TEAM_API_SECRET'),

        // Comma-separated emails that skip GG Prime verification entirely at
        // register/login — for test/QA accounts only, e.g. "a@test.com,b@test.com".
        'bypass_emails' => array_filter(array_map(
            'trim',
            explode(',', (string) env('TEAM_API_BYPASS_EMAILS', ''))
        )),
    ],

];

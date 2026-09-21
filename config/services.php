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

    'google_drive_leads' => [
        // Path to the Google service-account JSON key (keep it outside public/,
        // e.g. storage/app/google/drive-service-account.json — never commit it).
        'credentials_path' => env('GOOGLE_DRIVE_CREDENTIALS_PATH'),

        // The Drive file id of the leads Excel sheet — the {FILE_ID} segment in
        // https://drive.google.com/file/d/{FILE_ID}/view. The sheet must be
        // shared (Viewer) with the service account's own email address (found
        // inside the JSON key as "client_email").
        'file_id' => env('GOOGLE_DRIVE_LEADS_FILE_ID'),
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

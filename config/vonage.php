<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | If you're using API credentials, change these settings. Get your
    | credentials from https://dashboard.nexmo.com | 'Settings'.
    |
    */

    'api_key'    => function_exists('env') ? env('VONAGE_KEY', '') : '',
    'api_secret' => function_exists('env') ? env('VONAGE_SECRET', '') : '',

    /*
    |--------------------------------------------------------------------------
    | Signature Secret
    |--------------------------------------------------------------------------
    |
    | If you're using a signature secret, use this section. This can be used
    | without an `api_secret` for some APIs, as well as with an `api_secret`
    | for all APIs.
    |
    */

    'signature_secret' => function_exists('env') ? env('VONAGE_SIGNATURE_SECRET', '') : '',

    /*
    |--------------------------------------------------------------------------
    | Private Key
    |--------------------------------------------------------------------------
    |
    | Private keys are used to generate JWTs for authentication. Generation is
    | handled by the library. JWTs are required for newer APIs, such as voice
    | and media
    |
    */

    'private_key' => function_exists('env') ? env('VONAGE_PRIVATE_KEY', '') : '',
    'application_id' => function_exists('env') ? env('VONAGE_APPLICATION_ID', '') : '',

    /*
    |--------------------------------------------------------------------------
    | Application Identifiers
    |--------------------------------------------------------------------------
    |
    | Add an application name and version here to identify your application when
    | making API calls
    |
    */

    'app' => ['name' => function_exists('env') ? env('VONAGE_APP_NAME', 'VonageLaravel') : 'VonageLaravel',
              'version' => function_exists('env') ? env('VONAGE_APP_VERSION', '0.0.1') : '0.0.1'],

    /*
    |--------------------------------------------------------------------------
    | Client Override
    |--------------------------------------------------------------------------
    |
    | In the event you need to use this with vonage/client-core, this can be set
    | to provide a custom HTTP client.
    |
    */

    'http_client' => function_exists('env') ? env('VONAGE_HTTP_CLIENT', '') : '',

    /*
    |--------------------------------------------------------------------------
    | API Url Override
    |--------------------------------------------------------------------------
    |
    | For testing purposes you may want to change the URL that vonage/client
    | makes requests to from api.vonage.com to something else
    |
    */

    'base_api_url' => function_exists('env') ? env('VONAGE_BASE_API_URL') : null,
];
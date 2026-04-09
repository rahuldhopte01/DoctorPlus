<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Curobo Catalog API (Cannaleo)
    |--------------------------------------------------------------------------
    */
    'curobo_api_url' => env('CUROBO_CATALOG_API_URL', 'https://api.curobo.de'),
    'curobo_api_key' => env('CUROBO_CATALOG_API_KEY'),
    'catalog_sync_enabled' => env('CUROBO_CATALOG_SYNC_ENABLED', true),

    /*
    | Set to false only for local/dev when PHP has no CA bundle (e.g. WAMP).
    | Keep true in production. .env: CUROBO_CATALOG_SSL_VERIFY=false
    */
    'curobo_ssl_verify' => filter_var(env('CUROBO_CATALOG_SSL_VERIFY', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Curobo Prescription API (Cannaleo)
    |--------------------------------------------------------------------------
    */
    'prescription_api_path' => env('CUROBO_PRESCRIPTION_API_PATH', '/api/v1/prescription/'),
    'curobo_domain' => env('CUROBO_DOMAIN', ''),
    'prescription_callback_url' => env('CUROBO_PRESCRIPTION_CALLBACK_URL', ''),
    'default_signature_city' => env('CUROBO_DEFAULT_SIGNATURE_CITY', ''),

    /*
    | Static doctor signature (base64 image or URL) sent to Curobo until real doctor signature is implemented.
    | Optional. Example: data:image/png;base64,... or https://example.com/signature.png
    */
    'static_doctor_signature' => env('CUROBO_STATIC_DOCTOR_SIGNATURE', ''),
];

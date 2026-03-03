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
];

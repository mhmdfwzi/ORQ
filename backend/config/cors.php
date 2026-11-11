<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for cross-origin requests from the Next.js frontend.
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'POST'],

    'allowed_origins' => [
        'http://localhost:3000',
        env('FRONTEND_URL', 'https://orq-perfume.com'),
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'X-Revalidate-Token',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];

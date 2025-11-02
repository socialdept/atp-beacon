<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PLC Directory URL
    |--------------------------------------------------------------------------
    |
    | The URL of the PLC (Public Ledger of Credentials) directory used for
    | resolving DID:PLC identifiers. The default is the official AT Protocol
    | PLC directory.
    |
    */

    'plc_directory' => env('RESOLVER_PLC_DIRECTORY', 'https://plc.directory'),

    /*
    |--------------------------------------------------------------------------
    | PDS Endpoint
    |--------------------------------------------------------------------------
    |
    | The Personal Data Server endpoint used for handle resolution. This is
    | used when resolving handles to DIDs via the AT Protocol API.
    |
    */

    'pds_endpoint' => env('RESOLVER_PDS_ENDPOINT', 'https://bsky.social'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for HTTP requests to external services when
    | resolving DIDs and handles.
    |
    */

    'timeout' => env('RESOLVER_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for resolved DIDs and handles.
    | TTL values are in seconds.
    |
    */

    'cache' => [

        // Enable or disable caching globally
        'enabled' => env('RESOLVER_CACHE_ENABLED', true),

        // Cache TTL for DID documents (1 hour default)
        'did_ttl' => env('RESOLVER_CACHE_DID_TTL', 3600),

        // Cache TTL for handle resolutions (1 hour default)
        'handle_ttl' => env('RESOLVER_CACHE_HANDLE_TTL', 3600),

        // Cache TTL for PDS endpoints (1 hour default)
        'pds_ttl' => env('RESOLVER_CACHE_PDS_TTL', 3600),

    ],

];

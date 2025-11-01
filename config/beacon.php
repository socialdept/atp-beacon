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

    'plc_directory' => env('BEACON_PLC_DIRECTORY', 'https://plc.directory'),

    /*
    |--------------------------------------------------------------------------
    | PDS Endpoint
    |--------------------------------------------------------------------------
    |
    | The Personal Data Server endpoint used for handle resolution. This is
    | used when resolving handles to DIDs via the AT Protocol API.
    |
    */

    'pds_endpoint' => env('BEACON_PDS_ENDPOINT', 'https://bsky.social'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for HTTP requests to external services when
    | resolving DIDs, handles, and lexicons.
    |
    */

    'timeout' => env('BEACON_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching behavior for resolved DIDs, handles, and lexicons.
    | TTL values are in seconds.
    |
    */

    'cache' => [

        // Enable or disable caching globally
        'enabled' => env('BEACON_CACHE_ENABLED', true),

        // Cache TTL for DID documents (1 hour default)
        'did_ttl' => env('BEACON_CACHE_DID_TTL', 3600),

        // Cache TTL for handle resolutions (1 hour default)
        'handle_ttl' => env('BEACON_CACHE_HANDLE_TTL', 3600),

        // Cache TTL for PDS endpoints (1 hour default)
        'pds_ttl' => env('BEACON_CACHE_PDS_TTL', 3600),

        // Cache TTL for lexicon schemas (24 hours default)
        'lexicon_ttl' => env('BEACON_CACHE_LEXICON_TTL', 86400),

    ],

];
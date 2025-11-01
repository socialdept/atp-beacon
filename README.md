[![Beacon Header](./header.png)](https://github.com/socialdept/atp-signals)

<h3 align="center">
    Resolve AT Protocol identities in your Laravel application.
</h3>

<p align="center">
    <br>
    <a href="https://packagist.org/packages/socialdept/atp-beacon" title="Latest Version on Packagist"><img src="https://img.shields.io/packagist/v/socialdept/atp-beacon.svg?style=flat-square"></a>
    <a href="https://packagist.org/packages/socialdept/atp-beacon" title="Total Downloads"><img src="https://img.shields.io/packagist/dt/socialdept/atp-beacon.svg?style=flat-square"></a>
    <a href="https://github.com/socialdept/atp-beacon/actions/workflows/tests.yml" title="GitHub Tests Action Status"><img src="https://img.shields.io/github/actions/workflow/status/socialdept/atp-beacon/tests.yml?branch=main&label=tests&style=flat-square"></a>
    <a href="LICENSE" title="Software License"><img src="https://img.shields.io/github/license/socialdept/atp-beacon?style=flat-square"></a>
</p>

---

## What is Beacon?

**Beacon** is a Laravel package that resolves AT Protocol identities. Convert DIDs to handles, find PDS endpoints, and resolve DID documents with automatic caching and fallback support for both `did:plc` and `did:web` methods.

Think of it as a Swiss Army knife for AT Protocol identity resolution.

## Why use Beacon?

- **Simple API** - Resolve DIDs and handles with one method call
- **Automatic caching** - Smart caching with configurable TTLs
- **Multiple DID methods** - Support for `did:plc` and `did:web`
- **PDS discovery** - Find the correct PDS endpoint for any user
- **Production ready** - Battle-tested with proper error handling
- **Zero config** - Works out of the box with sensible defaults

## Quick Example

```php
use SocialDept\Beacon\Facades\Beacon;

// Resolve a DID to its document
$document = Beacon::resolveDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz');
$handle = $document->getHandle(); // "user.bsky.social"
$pds = $document->getPdsEndpoint(); // "https://bsky.social"

// Resolve a handle to its DID
$did = Beacon::resolveHandle('user.bsky.social');
// "did:plc:ewvi7nxzyoun6zhxrhs64oiz"

// Find someone's PDS endpoint
$pds = Beacon::resolvePds('alice.bsky.social');
// "https://bsky.social"
```

## Installation

```bash
composer require socialdept/atp-beacon
```

Beacon will auto-register with Laravel. Optionally publish the config:

```bash
php artisan vendor:publish --tag=beacon-config
```

## Basic Usage

### Resolving DIDs

Beacon supports both `did:plc` and `did:web` methods:

```php
use SocialDept\Beacon\Facades\Beacon;

// PLC directory resolution
$document = Beacon::resolveDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// Web DID resolution
$document = Beacon::resolveDid('did:web:example.com');

// Access document data
$handle = $document->getHandle();
$pdsEndpoint = $document->getPdsEndpoint();
$services = $document->service;
```

### Resolving Handles

Convert human-readable handles to DIDs:

```php
$did = Beacon::resolveHandle('alice.bsky.social');
// "did:plc:ewvi7nxzyoun6zhxrhs64oiz"

// Get the full DID document
$document = Beacon::resolveHandleToDid('alice.bsky.social');
```

### Finding PDS Endpoints

Automatically discover a user's Personal Data Server:

```php
// From a DID
$pds = Beacon::resolvePds('did:plc:ewvi7nxzyoun6zhxrhs64oiz');

// From a handle
$pds = Beacon::resolvePds('alice.bsky.social');

// Returns: "https://bsky.social" or user's custom PDS
```

This is particularly useful when you need to make API calls to a user's PDS instead of hardcoding Bluesky's public instance.

### Cache Management

Beacon automatically caches resolutions. Clear the cache when needed:

```php
// Clear specific DID cache
Beacon::clearDidCache('did:plc:abc123');

// Clear specific handle cache
Beacon::clearHandleCache('alice.bsky.social');

// Clear specific PDS cache
Beacon::clearPdsCache('alice.bsky.social');

// Clear all cached data
Beacon::clearCache();
```

### Disable Caching

Pass `false` as the second parameter to bypass cache:

```php
$document = Beacon::resolveDid('did:plc:abc123', useCache: false);
$did = Beacon::resolveHandle('alice.bsky.social', useCache: false);
$pds = Beacon::resolvePds('alice.bsky.social', useCache: false);
```

### Identity Validation

Beacon includes static helper methods to validate DIDs and handles:

```php
use SocialDept\Beacon\Support\Identity;

// Validate handles
Identity::isHandle('alice.bsky.social'); // true
Identity::isHandle('invalid');           // false

// Validate DIDs
Identity::isDid('did:plc:ewvi7nxzyoun6zhxrhs64oiz'); // true
Identity::isDid('did:web:example.com');              // true
Identity::isDid('invalid');                          // false

// Extract DID method
Identity::extractDidMethod('did:plc:abc123'); // "plc"
Identity::extractDidMethod('did:web:test');   // "web"

// Check specific DID types
Identity::isPlcDid('did:plc:abc123'); // true
Identity::isWebDid('did:web:test');   // true
```

These helpers are useful for validating user input before making resolution calls.

## Configuration

Beacon works great with zero configuration, but you can customize behavior in `config/beacon.php`:

```php
return [
    // PLC directory for did:plc resolution
    'plc_directory' => env('BEACON_PLC_DIRECTORY', 'https://plc.directory'),

    // Default PDS endpoint for handle resolution
    'pds_endpoint' => env('BEACON_PDS_ENDPOINT', 'https://bsky.social'),

    // HTTP request timeout
    'timeout' => env('BEACON_TIMEOUT', 10),

    // Cache configuration
    'cache' => [
        'enabled' => env('BEACON_CACHE_ENABLED', true),

        // Cache TTL for DID documents (1 hour)
        'did_ttl' => env('BEACON_CACHE_DID_TTL', 3600),

        // Cache TTL for handle resolutions (1 hour)
        'handle_ttl' => env('BEACON_CACHE_HANDLE_TTL', 3600),

        // Cache TTL for PDS endpoints (1 hour)
        'pds_ttl' => env('BEACON_CACHE_PDS_TTL', 3600),
    ],
];
```

## API Reference

### Available Methods

```php
// DID Resolution
Beacon::resolveDid(string $did, bool $useCache = true): DidDocument

// Handle Resolution
Beacon::handleToDid(string $handle, bool $useCache = true): string
Beacon::resolveHandle(string $handle, bool $useCache = true): DidDocument

// Identity Resolution
Beacon::resolveIdentity(string $actor, bool $useCache = true): DidDocument

// PDS Resolution
Beacon::resolvePds(string $actor, bool $useCache = true): ?string

// Cache Management
Beacon::clearDidCache(string $did): void
Beacon::clearHandleCache(string $handle): void
Beacon::clearPdsCache(string $actor): void
Beacon::clearCache(): void

// Identity Validation (static helpers)
Identity::isHandle(?string $handle): bool
Identity::isDid(?string $did): bool
Identity::extractDidMethod(string $did): ?string
Identity::isPlcDid(string $did): bool
Identity::isWebDid(string $did): bool
```

### DidDocument Object

```php
$document->id;                    // string - The DID
$document->alsoKnownAs;           // array - Alternative identifiers
$document->verificationMethod;    // array - Verification methods
$document->service;               // array - Service endpoints
$document->raw;                   // array - Raw DID document

// Helper methods
$document->getHandle();           // ?string - Extract handle from alsoKnownAs
$document->getPdsEndpoint();      // ?string - Extract PDS service endpoint
$document->toArray();             // array - Convert to array
```

## Error Handling

Beacon throws descriptive exceptions when resolution fails:

```php
use SocialDept\Beacon\Exceptions\DidResolutionException;
use SocialDept\Beacon\Exceptions\HandleResolutionException;

try {
    $document = Beacon::resolveDid('did:invalid:format');
} catch (DidResolutionException $e) {
    // Handle DID resolution errors
    logger()->error('DID resolution failed', [
        'message' => $e->getMessage(),
    ]);
}

try {
    $did = Beacon::resolveHandle('invalid-handle');
} catch (HandleResolutionException $e) {
    // Handle handle resolution errors
}
```

## Use Cases

### Building an AppView

```php
// Resolve user identity from DID
$document = Beacon::resolveDid($event->did);
$handle = $document->getHandle();

// Make authenticated requests to their PDS
$pds = Beacon::resolvePds($event->did);
$client = new AtProtoClient($pds);
```

### Custom Feed Generators

```php
// Resolve multiple handles efficiently (caching kicks in)
$dids = collect(['alice.bsky.social', 'bob.bsky.social'])
    ->map(fn($handle) => Beacon::resolveHandle($handle))
    ->all();
```

### Profile Resolution

```php
// Get complete identity information
$document = Beacon::resolveHandleToDid($username);

$profile = [
    'did' => $document->id,
    'handle' => $document->getHandle(),
    'pds' => $document->getPdsEndpoint(),
];
```

### Input Validation

```php
use SocialDept\Beacon\Support\Identity;
use SocialDept\Beacon\Facades\Beacon;

// Validate user input before resolving
$userInput = request()->input('identifier');

if (Identity::isHandle($userInput)) {
    $did = Beacon::resolveHandle($userInput);
} elseif (Identity::isDid($userInput)) {
    $document = Beacon::resolveDid($userInput);
} else {
    abort(422, 'Invalid handle or DID');
}
```

## Requirements

- PHP 8.2+
- Laravel 11+
- `ext-gmp` extension

## Resources

- [AT Protocol Documentation](https://atproto.com/)
- [DID:PLC Specification](https://github.com/did-method-plc/did-method-plc)
- [DID:Web Specification](https://w3c-ccg.github.io/did-method-web/)
- [PLC Directory](https://plc.directory/)

## Support & Contributing

Found a bug or have a feature request? [Open an issue](https://github.com/socialdept/atp-beacon/issues).

Want to contribute? We'd love your help! Check out the [contribution guidelines](CONTRIBUTING.md).

## Credits

- [Miguel Batres](https://batres.co) - founder & lead maintainer
- [All contributors](https://github.com/socialdept/atp-beacon/graphs/contributors)

## License

Beacon is open-source software licensed under the [MIT license](LICENSE).

---

**Built for the Federation** • By Social Dept.

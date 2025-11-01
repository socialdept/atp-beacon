<?php

namespace SocialDept\Beacon\Resolvers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SocialDept\Beacon\Contracts\DidResolver;
use SocialDept\Beacon\Data\DidDocument;
use SocialDept\Beacon\Exceptions\DidResolutionException;
use SocialDept\Beacon\Support\Concerns\HasConfig;
use SocialDept\Beacon\Support\Concerns\ParsesDid;

class PlcDidResolver implements DidResolver
{
    use HasConfig;
    use ParsesDid;

    protected Client $client;

    protected string $plcDirectory;

    /**
     * Create a new PLC DID resolver instance.
     *
     * @param  string  $plcDirectory  The PLC directory URL
     */
    public function __construct(?string $plcDirectory = null, ?int $timeout = null)
    {
        $this->plcDirectory = $plcDirectory ?? $this->getConfig('beacon.plc_directory', 'https://plc.directory');
        $this->client = new Client([
            'timeout' => $timeout ?? $this->getConfig('beacon.timeout', 10),
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'Beacon/1.0',
            ],
        ]);
    }

    /**
     * Resolve a DID:PLC to a DID Document.
     *
     * @param  string  $did  The DID to resolve (e.g., "did:plc:abc123")
     */
    public function resolve(string $did): DidDocument
    {
        if (! $this->supports($this->extractMethod($did))) {
            throw DidResolutionException::unsupportedMethod($this->extractMethod($did));
        }

        try {
            $response = $this->client->get("{$this->plcDirectory}/{$did}");
            $data = json_decode($response->getBody()->getContents(), true);

            if (! is_array($data)) {
                throw DidResolutionException::resolutionFailed($did, 'Invalid response format');
            }

            return DidDocument::fromArray($data);
        } catch (GuzzleException $e) {
            throw DidResolutionException::resolutionFailed($did, $e->getMessage());
        }
    }

    /**
     * Check if this resolver supports the given DID method.
     *
     * @param  string  $method  The DID method (e.g., "plc")
     */
    public function supports(string $method): bool
    {
        return $method === 'plc';
    }
}

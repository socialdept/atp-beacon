<?php

namespace SocialDept\Resolver\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SocialDept\Resolver\Resolver;
use SocialDept\Resolver\Contracts\CacheStore;
use SocialDept\Resolver\Contracts\DidResolver;
use SocialDept\Resolver\Contracts\HandleResolver;
use SocialDept\Resolver\Data\DidDocument;

class ResolverIdentityTest extends TestCase
{
    public function test_it_can_convert_handle_to_did(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $handleResolver->expects($this->once())
            ->method('resolve')
            ->with('user.bsky.social')
            ->willReturn('did:plc:abc123');

        $cache->method('has')->willReturn(false);
        $cache->expects($this->once())
            ->method('put')
            ->with('handle:user.bsky.social', 'did:plc:abc123', $this->anything());

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $did = $beacon->handleToDid('user.bsky.social');

        $this->assertSame('did:plc:abc123', $did);
    }

    public function test_it_can_resolve_handle(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $handleResolver->expects($this->once())
            ->method('resolve')
            ->with('user.bsky.social')
            ->willReturn('did:plc:abc123');

        $didDocument = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'alsoKnownAs' => ['at://user.bsky.social'],
        ]);

        $didResolver->expects($this->once())
            ->method('resolve')
            ->with('did:plc:abc123')
            ->willReturn($didDocument);

        $cache->method('has')->willReturn(false);

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $document = $beacon->resolveHandle('user.bsky.social');

        $this->assertInstanceOf(DidDocument::class, $document);
        $this->assertSame('did:plc:abc123', $document->id);
    }

    public function test_it_can_resolve_identity_with_did(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $didDocument = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
        ]);

        $didResolver->expects($this->once())
            ->method('resolve')
            ->with('did:plc:abc123')
            ->willReturn($didDocument);

        $cache->method('has')->willReturn(false);
        $handleResolver->expects($this->never())->method('resolve');

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $document = $beacon->resolveIdentity('did:plc:abc123');

        $this->assertInstanceOf(DidDocument::class, $document);
        $this->assertSame('did:plc:abc123', $document->id);
    }

    public function test_it_can_resolve_identity_with_handle(): void
    {
        $didResolver = $this->createMock(DidResolver::class);
        $handleResolver = $this->createMock(HandleResolver::class);
        $cache = $this->createMock(CacheStore::class);

        $handleResolver->expects($this->once())
            ->method('resolve')
            ->with('user.bsky.social')
            ->willReturn('did:plc:abc123');

        $didDocument = DidDocument::fromArray([
            'id' => 'did:plc:abc123',
            'alsoKnownAs' => ['at://user.bsky.social'],
        ]);

        $didResolver->expects($this->once())
            ->method('resolve')
            ->with('did:plc:abc123')
            ->willReturn($didDocument);

        $cache->method('has')->willReturn(false);

        $beacon = new Resolver($didResolver, $handleResolver, $cache);
        $document = $beacon->resolveIdentity('user.bsky.social');

        $this->assertInstanceOf(DidDocument::class, $document);
        $this->assertSame('did:plc:abc123', $document->id);
        $this->assertSame('user.bsky.social', $document->getHandle());
    }
}

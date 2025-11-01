<?php

namespace SocialDept\Beacon\Contracts;

interface HandleResolver
{
    /**
     * Resolve a handle to a DID.
     *
     * @param  string  $handle  The handle to resolve (e.g., "user.bsky.social")
     * @return string The resolved DID
     *
     * @throws \SocialDept\Beacon\Exceptions\HandleResolutionException
     */
    public function resolve(string $handle): string;
}

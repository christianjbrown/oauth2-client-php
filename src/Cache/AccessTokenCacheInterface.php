<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Cache;

use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;

interface AccessTokenCacheInterface
{
    /**
     * The cached access token with its remaining lifetime, or null when none is cached or it has expired.
     */
    public function get(): ?AccessTokenInterface;

    public function store(AccessTokenInterface $accessToken): void;
}

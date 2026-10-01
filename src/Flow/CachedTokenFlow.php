<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Flow;

use ChristianBrown\OAuth2Client\Cache\AccessTokenCacheInterface;
use ChristianBrown\OAuth2Client\Grant\TokenGrantInterface;
use ChristianBrown\OAuth2Client\Lock\LockInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use Throwable;

final class CachedTokenFlow implements CachedTokenFlowInterface
{
    private AccessTokenCacheInterface $cache;
    private LockInterface $lock;

    public function __construct(AccessTokenCacheInterface $cache, LockInterface $lock)
    {
        $this->cache = $cache;
        $this->lock = $lock;
    }

    public function getAccessToken(TokenGrantInterface $grant, bool $forceNew): AccessTokenInterface
    {
        $cachedAccessToken = $this->getCachedAccessToken($forceNew);
        if (null !== $cachedAccessToken) {
            return $cachedAccessToken;
        }

        // Serialise the request so a rotating refresh token is not spent by two
        // concurrent refreshes. Release explicitly on both the success and
        // failure paths (rather than a `finally`, whose implicit exception edge
        // leaves an unreachable path) so the lock is always freed.
        $this->lock->acquire();

        try {
            $accessToken = $this->getCachedOrRequestedAccessToken($grant, $forceNew);
        } catch (Throwable $exception) {
            $this->lock->release();

            throw $exception;
        }

        $this->lock->release();

        return $accessToken;
    }

    private function getCachedAccessToken(bool $forceNew): ?AccessTokenInterface
    {
        if ($forceNew) {
            return null;
        }

        return $this->cache->get();
    }

    /**
     * Re-read the cache and, only if it is still empty, request a token. Called
     * under the lock: another process may have refreshed while we waited for it,
     * in which case its freshly stored token is returned without a new call.
     */
    private function getCachedOrRequestedAccessToken(TokenGrantInterface $grant, bool $forceNew): AccessTokenInterface
    {
        $cachedAccessToken = $this->getCachedAccessToken($forceNew);
        if (null !== $cachedAccessToken) {
            return $cachedAccessToken;
        }

        $accessToken = $grant->requestToken();
        $this->cache->store($accessToken);

        return $accessToken;
    }
}

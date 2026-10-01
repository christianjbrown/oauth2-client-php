<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Cache;

use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculatorInterface;
use ChristianBrown\OAuth2Client\Model\AccessToken;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;

final class AccessTokenCache implements AccessTokenCacheInterface
{
    private TtlAwareKeyValueStoreInterface $accessTokenKeyValueStore;
    private TokenExpiryCalculatorInterface $expiryCalculator;

    public function __construct(TtlAwareKeyValueStoreInterface $accessTokenKeyValueStore, TokenExpiryCalculatorInterface $expiryCalculator)
    {
        $this->accessTokenKeyValueStore = $accessTokenKeyValueStore;
        $this->expiryCalculator = $expiryCalculator;
    }

    public function get(): ?AccessTokenInterface
    {
        $value = $this->accessTokenKeyValueStore->getValue();
        if (empty($value)) {
            return null;
        }

        $ttl = $this->accessTokenKeyValueStore->getTtl();
        if (null === $ttl) {
            return null;
        }

        // $ttl is the absolute expiry epoch stored at fetch time; AccessToken
        // expects a relative lifetime, so return the seconds still remaining.
        $remaining = $this->expiryCalculator->remainingLifetime($ttl);
        if ($remaining <= 0) {
            return null;
        }

        return new AccessToken($value, $remaining);
    }

    public function store(AccessTokenInterface $accessToken): void
    {
        $this->accessTokenKeyValueStore->setValue($accessToken->getAccessToken(), $this->expiryCalculator->expiresAt($accessToken->getExpiresIn()));
    }
}

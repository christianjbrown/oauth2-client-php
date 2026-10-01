<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client;

use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\ClientAuthenticationInterface;
use ChristianBrown\OAuth2Client\Cache\AccessTokenCache;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpoint;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifier;
use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculator;
use ChristianBrown\OAuth2Client\Flow\CachedTokenFlow;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrantFactory;
use ChristianBrown\OAuth2Client\Lock\LockInterface;
use ChristianBrown\OAuth2Client\Transformer\AccessTokenTransformer;
use Psr\Clock\ClockInterface;

/**
 * Composition root for the refresh token grant.
 */
final class RefreshTokenManagerFactory implements RefreshTokenManagerFactoryInterface
{
    private ClockInterface $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function create(JsonApiRequestSenderInterface $apiRequestSender, TtlAwareKeyValueStoreInterface $accessTokenKeyValueStore, KeyValueStoreInterface $refreshTokenKeyValueStore, string $url, ClientAuthenticationInterface $clientAuthentication, LockInterface $lock): RefreshTokenManagerInterface
    {
        $cache = new AccessTokenCache($accessTokenKeyValueStore, new TokenExpiryCalculator($this->clock));
        $endpoint = new TokenEndpoint($apiRequestSender, new AccessTokenTransformer(), $url);

        return new RefreshTokenManager(
            new CachedTokenFlow($cache, $lock),
            new RefreshTokenGrantFactory($endpoint, $refreshTokenKeyValueStore, $clientAuthentication, new InvalidGrantClassifier()),
        );
    }
}

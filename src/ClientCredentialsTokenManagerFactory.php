<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client;

use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Cache\AccessTokenCache;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpoint;
use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculator;
use ChristianBrown\OAuth2Client\Flow\CachedTokenFlow;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrantFactory;
use ChristianBrown\OAuth2Client\Lock\LockInterface;
use ChristianBrown\OAuth2Client\Transformer\AccessTokenTransformer;
use Psr\Clock\ClockInterface;

/**
 * Composition root for the client credentials grant.
 */
final class ClientCredentialsTokenManagerFactory implements ClientCredentialsTokenManagerFactoryInterface
{
    private ClockInterface $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function create(JsonApiRequestSenderInterface $apiRequestSender, TtlAwareKeyValueStoreInterface $accessTokenKeyValueStore, string $url, LockInterface $lock): ClientCredentialsTokenManagerInterface
    {
        $cache = new AccessTokenCache($accessTokenKeyValueStore, new TokenExpiryCalculator($this->clock));

        return new ClientCredentialsTokenManager(
            new CachedTokenFlow($cache, $lock),
            new ClientCredentialsGrantFactory(new TokenEndpoint($apiRequestSender, new AccessTokenTransformer(), $url)),
        );
    }
}

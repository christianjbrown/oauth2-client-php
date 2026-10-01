<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client;

use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\ClientAuthenticationInterface;
use ChristianBrown\OAuth2Client\Lock\LockInterface;

interface RefreshTokenManagerFactoryInterface
{
    public function create(JsonApiRequestSenderInterface $apiRequestSender, TtlAwareKeyValueStoreInterface $accessTokenKeyValueStore, KeyValueStoreInterface $refreshTokenKeyValueStore, string $url, ClientAuthenticationInterface $clientAuthentication, LockInterface $lock): RefreshTokenManagerInterface;
}

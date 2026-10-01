<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client;

use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Lock\LockInterface;

interface ClientCredentialsTokenManagerFactoryInterface
{
    public function create(JsonApiRequestSenderInterface $apiRequestSender, TtlAwareKeyValueStoreInterface $accessTokenKeyValueStore, string $url, LockInterface $lock): ClientCredentialsTokenManagerInterface;
}

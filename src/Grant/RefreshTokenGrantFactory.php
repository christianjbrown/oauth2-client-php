<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\ClientAuthenticationInterface;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifierInterface;

final class RefreshTokenGrantFactory implements RefreshTokenGrantFactoryInterface
{
    private ClientAuthenticationInterface $clientAuthentication;
    private TokenEndpointInterface $endpoint;
    private InvalidGrantClassifierInterface $invalidGrantClassifier;
    private KeyValueStoreInterface $refreshTokenKeyValueStore;

    public function __construct(TokenEndpointInterface $endpoint, KeyValueStoreInterface $refreshTokenKeyValueStore, ClientAuthenticationInterface $clientAuthentication, InvalidGrantClassifierInterface $invalidGrantClassifier)
    {
        $this->clientAuthentication = $clientAuthentication;
        $this->endpoint = $endpoint;
        $this->invalidGrantClassifier = $invalidGrantClassifier;
        $this->refreshTokenKeyValueStore = $refreshTokenKeyValueStore;
    }

    public function create(string $clientId): RefreshTokenGrantInterface
    {
        return new RefreshTokenGrant($clientId, $this->endpoint, $this->refreshTokenKeyValueStore, $this->clientAuthentication, $this->invalidGrantClassifier);
    }
}

<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;

final class ClientCredentialsGrantFactory implements ClientCredentialsGrantFactoryInterface
{
    private TokenEndpointInterface $endpoint;

    public function __construct(TokenEndpointInterface $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function create(string $basicAuthValue, ?string $scope = null, ?string $clientId = null): ClientCredentialsGrantInterface
    {
        return new ClientCredentialsGrant($basicAuthValue, $scope, $clientId, $this->endpoint);
    }
}

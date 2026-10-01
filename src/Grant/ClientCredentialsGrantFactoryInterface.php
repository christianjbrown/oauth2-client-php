<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

interface ClientCredentialsGrantFactoryInterface
{
    public function create(string $basicAuthValue, ?string $scope = null, ?string $clientId = null): ClientCredentialsGrantInterface;
}

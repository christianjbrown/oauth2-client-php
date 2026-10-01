<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

interface RefreshTokenGrantFactoryInterface
{
    public function create(string $clientId): RefreshTokenGrantInterface;
}

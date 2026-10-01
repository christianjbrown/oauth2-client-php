<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Authentication;

/**
 * How a client identifies itself to the token endpoint when refreshing.
 */
interface ClientAuthenticationInterface
{
    /**
     * @return array<string, string>
     */
    public function getBodyFields(string $clientId): array;

    /**
     * @return array<string, string>
     */
    public function getHeaders(string $clientId): array;
}

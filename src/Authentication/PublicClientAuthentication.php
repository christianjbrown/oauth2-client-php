<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Authentication;

use ChristianBrown\OAuth2Client\TokenManagerInterface;

/**
 * A public client has no secret and sends its client_id in the request body.
 */
final class PublicClientAuthentication implements PublicClientAuthenticationInterface
{
    /**
     * @return array<string, string>
     */
    public function getBodyFields(string $clientId): array
    {
        return [TokenManagerInterface::REQUEST_KEY_CLIENT_ID => $clientId];
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(string $clientId): array
    {
        return [];
    }
}

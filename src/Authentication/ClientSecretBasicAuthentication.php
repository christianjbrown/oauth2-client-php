<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Authentication;

use ChristianBrown\OAuth2Client\TokenManagerInterface;

use function base64_encode;
use function sprintf;

/**
 * A confidential client authenticates with HTTP Basic using its id and secret.
 */
final class ClientSecretBasicAuthentication implements ClientSecretBasicAuthenticationInterface
{
    private string $clientSecret;

    public function __construct(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return array<string, string>
     */
    public function getBodyFields(string $clientId): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(string $clientId): array
    {
        return [TokenManagerInterface::HEADER_KEY_AUTHORIZATION => sprintf(TokenManagerInterface::BASIC_AUTH_VALUE_SPRINTF, base64_encode($clientId.':'.$this->clientSecret))];
    }
}

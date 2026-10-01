<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\GrantType;
use ChristianBrown\OAuth2Client\TokenManagerInterface;

use function array_filter;
use function base64_encode;
use function sprintf;

final class ClientCredentialsGrant implements ClientCredentialsGrantInterface
{
    private string $basicAuthValue;
    private ?string $clientId;
    private TokenEndpointInterface $endpoint;
    private ?string $scope;

    public function __construct(string $basicAuthValue, ?string $scope, ?string $clientId, TokenEndpointInterface $endpoint)
    {
        $this->basicAuthValue = $basicAuthValue;
        $this->clientId = $clientId;
        $this->endpoint = $endpoint;
        $this->scope = $scope;
    }

    public function requestToken(): AccessTokenInterface
    {
        $headers = [
            TokenManagerInterface::HEADER_KEY_CONTENT_TYPE => TokenManagerInterface::HEADER_VALUE_CONTENT_TYPE_FORM,
            TokenManagerInterface::HEADER_KEY_AUTHORIZATION => sprintf(TokenManagerInterface::BASIC_AUTH_VALUE_SPRINTF, base64_encode($this->basicAuthValue)),
        ];
        $bodyData = array_filter([
            TokenManagerInterface::REQUEST_KEY_GRANT_TYPE => GrantType::CLIENT_CREDENTIALS->value,
            TokenManagerInterface::REQUEST_KEY_SCOPE => $this->scope,
            TokenManagerInterface::REQUEST_KEY_CLIENT_ID => $this->clientId,
        ]);

        // @todo Could probably handle 401/403 more specifically
        return $this->endpoint->requestToken($headers, $bodyData);
    }
}

<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Endpoint;

use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\BadResponsePayloadFieldExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;

interface TokenEndpointInterface
{
    /**
     * POSTs a form to the token endpoint and transforms the response into an access token.
     *
     * @param array<string, string> $headers
     * @param array<string, string> $bodyData
     *
     * @throws RequestExceptionInterface
     * @throws BadResponsePayloadFieldExceptionInterface
     */
    public function requestToken(array $headers, array $bodyData): AccessTokenInterface;
}

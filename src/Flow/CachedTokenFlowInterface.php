<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Flow;

use ChristianBrown\OAuth2Client\Grant\TokenGrantInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\BadResponsePayloadFieldExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\InvalidGrantExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;

/**
 * The flow every grant shares: return the cached token if it is valid, else
 * take the lock, re-check, request a token through the grant and cache it.
 */
interface CachedTokenFlowInterface
{
    /**
     * @throws RequestExceptionInterface
     * @throws InvalidGrantExceptionInterface
     * @throws BadResponsePayloadFieldExceptionInterface
     */
    public function getAccessToken(TokenGrantInterface $grant, bool $forceNew): AccessTokenInterface;
}

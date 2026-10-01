<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\BadResponsePayloadFieldExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\InvalidGrantExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;

/**
 * One grant-specific token request, already bound to its inputs. Adding a grant
 * type means adding an implementation of this, not editing the shared flow.
 */
interface TokenGrantInterface
{
    /**
     * @throws RequestExceptionInterface
     * @throws InvalidGrantExceptionInterface
     * @throws BadResponsePayloadFieldExceptionInterface
     */
    public function requestToken(): AccessTokenInterface;
}

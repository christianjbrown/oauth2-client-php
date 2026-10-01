<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Error;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;

interface InvalidGrantClassifierInterface
{
    /**
     * Whether a failed token request was rejected with the OAuth `invalid_grant` error.
     */
    public function isInvalidGrant(ExceptionInterface $exception): bool;
}

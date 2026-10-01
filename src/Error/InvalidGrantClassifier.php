<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Error;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;
use ChristianBrown\ApiClient\Exception\Response\ResponseExceptionInterface;
use ChristianBrown\OAuth2Client\TokenManagerInterface;

final class InvalidGrantClassifier implements InvalidGrantClassifierInterface
{
    public function isInvalidGrant(ExceptionInterface $exception): bool
    {
        if (!$exception instanceof ResponseExceptionInterface) {
            return false;
        }

        $decoded = $exception->getDecodedBody();
        if (null === $decoded) {
            return false;
        }

        if (!isset($decoded[TokenManagerInterface::RESPONSE_KEY_ERROR])) {
            return false;
        }

        return TokenManagerInterface::ERROR_INVALID_GRANT === $decoded[TokenManagerInterface::RESPONSE_KEY_ERROR];
    }
}

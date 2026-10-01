<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Expiry;

interface TokenExpiryCalculatorInterface
{
    /**
     * The absolute expiry epoch, in seconds, of a token that lives for $expiresIn seconds from now.
     */
    public function expiresAt(int $expiresIn): int;

    /**
     * The seconds left until the absolute expiry epoch. Zero or less means expired.
     */
    public function remainingLifetime(int $expiresAt): int;
}

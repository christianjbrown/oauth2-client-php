<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Expiry;

use Psr\Clock\ClockInterface;

final class TokenExpiryCalculator implements TokenExpiryCalculatorInterface
{
    private ClockInterface $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function expiresAt(int $expiresIn): int
    {
        return $this->clock->now()->getTimestamp() + $expiresIn;
    }

    public function remainingLifetime(int $expiresAt): int
    {
        return $expiresAt - $this->clock->now()->getTimestamp();
    }
}

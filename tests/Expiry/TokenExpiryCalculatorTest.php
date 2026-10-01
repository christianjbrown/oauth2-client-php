<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Expiry;

use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[CoversClass(TokenExpiryCalculator::class)]
final class TokenExpiryCalculatorTest extends TestCase
{
    public function testExpiresAtAddsLifetimeToNow(): void
    {
        $calculator = new TokenExpiryCalculator(new MockClock('@1000'));

        self::assertSame(1042, $calculator->expiresAt(42));
    }

    public function testRemainingLifetimeIsTimeLeftUntilExpiry(): void
    {
        $clock = new MockClock('@1000');
        $calculator = new TokenExpiryCalculator($clock);

        self::assertSame(42, $calculator->remainingLifetime(1042));

        $clock->sleep(50);

        self::assertSame(-8, $calculator->remainingLifetime(1042));
    }
}

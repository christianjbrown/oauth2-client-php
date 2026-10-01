<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Lock;

use ChristianBrown\OAuth2Client\Lock\NullLock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullLock::class)]
final class NullLockTest extends TestCase
{
    public function testAcquireAndReleaseDoNothing(): void
    {
        $lock = new NullLock();

        $lock->acquire();
        $lock->release();

        $this->expectNotToPerformAssertions();
    }
}

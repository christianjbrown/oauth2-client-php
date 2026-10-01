<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Lock;

/**
 * A lock that never blocks, for deployments where only one process refreshes
 * tokens at a time.
 */
final class NullLock implements NullLockInterface
{
    public function acquire(): void
    {
    }

    public function release(): void
    {
    }
}

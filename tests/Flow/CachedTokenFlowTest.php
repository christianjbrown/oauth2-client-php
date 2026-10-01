<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Flow;

use ChristianBrown\OAuth2Client\Cache\AccessTokenCacheInterface;
use ChristianBrown\OAuth2Client\Flow\CachedTokenFlow;
use ChristianBrown\OAuth2Client\Grant\TokenGrantInterface;
use ChristianBrown\OAuth2Client\Lock\LockInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(CachedTokenFlow::class)]
final class CachedTokenFlowTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testForceNewSkipsBothCacheReads(): void
    {
        $fresh = self::createStub(AccessTokenInterface::class);

        $cache = self::createMock(AccessTokenCacheInterface::class);
        $cache->expects(self::never())->method('get');
        $cache->expects(self::once())->method('store')->with($fresh);

        $lock = self::createMock(LockInterface::class);
        $lock->expects(self::once())->method('acquire');
        $lock->expects(self::once())->method('release');

        $grant = self::createStub(TokenGrantInterface::class);
        $grant->method('requestToken')->willReturn($fresh);

        self::assertSame($fresh, (new CachedTokenFlow($cache, $lock))->getAccessToken($grant, true));
    }

    /**
     * @throws Exception
     */
    public function testReleasesTheLockWhenTheRequestFails(): void
    {
        $failure = new RuntimeException('boom');

        $cache = self::createStub(AccessTokenCacheInterface::class);
        $cache->method('get')->willReturn(null);

        $lock = self::createMock(LockInterface::class);
        $lock->expects(self::once())->method('acquire');
        $lock->expects(self::once())->method('release');

        $grant = self::createStub(TokenGrantInterface::class);
        $grant->method('requestToken')->willThrowException($failure);

        try {
            (new CachedTokenFlow($cache, $lock))->getAccessToken($grant, false);
            self::fail('Expected the failure to propagate.');
        } catch (RuntimeException $e) {
            self::assertSame($failure, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function testRequestsStoresAndReleasesWhenNothingIsCached(): void
    {
        $fresh = self::createStub(AccessTokenInterface::class);

        $cache = self::createMock(AccessTokenCacheInterface::class);
        $cache->expects(self::exactly(2))->method('get')->willReturn(null);
        $cache->expects(self::once())->method('store')->with($fresh);

        $lock = self::createMock(LockInterface::class);
        $lock->expects(self::once())->method('acquire');
        $lock->expects(self::once())->method('release');

        $grant = self::createMock(TokenGrantInterface::class);
        $grant->expects(self::once())->method('requestToken')->willReturn($fresh);

        self::assertSame($fresh, (new CachedTokenFlow($cache, $lock))->getAccessToken($grant, false));
    }

    /**
     * @throws Exception
     */
    public function testReturnsCachedTokenWithoutLockingOrRequesting(): void
    {
        $cached = self::createStub(AccessTokenInterface::class);
        $cache = self::createStub(AccessTokenCacheInterface::class);
        $cache->method('get')->willReturn($cached);

        $lock = self::createMock(LockInterface::class);
        $lock->expects(self::never())->method('acquire');

        $grant = self::createMock(TokenGrantInterface::class);
        $grant->expects(self::never())->method('requestToken');

        self::assertSame($cached, (new CachedTokenFlow($cache, $lock))->getAccessToken($grant, false));
    }

    /**
     * @throws Exception
     */
    public function testReturnsTokenRefreshedWhileWaitingForTheLock(): void
    {
        $cached = self::createStub(AccessTokenInterface::class);

        $cache = self::createMock(AccessTokenCacheInterface::class);
        $cache->expects(self::exactly(2))->method('get')->willReturnOnConsecutiveCalls(null, $cached);
        $cache->expects(self::never())->method('store');

        $lock = self::createMock(LockInterface::class);
        $lock->expects(self::once())->method('acquire');
        $lock->expects(self::once())->method('release');

        $grant = self::createMock(TokenGrantInterface::class);
        $grant->expects(self::never())->method('requestToken');

        self::assertSame($cached, (new CachedTokenFlow($cache, $lock))->getAccessToken($grant, false));
    }
}

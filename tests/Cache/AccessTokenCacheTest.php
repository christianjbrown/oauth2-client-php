<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Cache;

use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Cache\AccessTokenCache;
use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculator;
use ChristianBrown\OAuth2Client\Model\AccessToken;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[CoversClass(AccessTokenCache::class)]
#[CoversClass(AccessToken::class)]
#[CoversClass(TokenExpiryCalculator::class)]
final class AccessTokenCacheTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[TestWith([null, null])]
    #[TestWith(['', 1100])]
    #[TestWith(['test-token', null])]
    #[TestWith(['test-token', 1000])]
    #[TestWith(['test-token', 900])]
    public function testGetReturnsNullWhenNothingUsableIsCached(?string $value, ?int $ttl): void
    {
        $store = self::createStub(TtlAwareKeyValueStoreInterface::class);
        $store->method('getValue')->willReturn($value);
        $store->method('getTtl')->willReturn($ttl);

        $cache = new AccessTokenCache($store, new TokenExpiryCalculator(new MockClock('@1000')));

        self::assertNull($cache->get());
    }

    /**
     * @throws Exception
     */
    public function testGetReturnsTokenWithRemainingLifetime(): void
    {
        $store = self::createStub(TtlAwareKeyValueStoreInterface::class);
        $store->method('getValue')->willReturn('test-token');
        $store->method('getTtl')->willReturn(1042);

        $cache = new AccessTokenCache($store, new TokenExpiryCalculator(new MockClock('@1000')));

        $token = $cache->get();

        self::assertNotNull($token);
        self::assertSame('test-token', $token->getAccessToken());
        self::assertSame(42, $token->getExpiresIn());
    }

    /**
     * @throws Exception
     */
    public function testStoreWritesTokenWithAbsoluteExpiry(): void
    {
        $accessToken = self::createStub(AccessTokenInterface::class);
        $accessToken->method('getAccessToken')->willReturn('test-token');
        $accessToken->method('getExpiresIn')->willReturn(42);

        $store = self::createMock(TtlAwareKeyValueStoreInterface::class);
        $store->expects(self::once())
            ->method('setValue')
            ->with('test-token', 1042);

        $cache = new AccessTokenCache($store, new TokenExpiryCalculator(new MockClock('@1000')));
        $cache->store($accessToken);
    }
}

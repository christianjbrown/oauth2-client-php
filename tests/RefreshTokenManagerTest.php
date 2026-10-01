<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests;

use ChristianBrown\OAuth2Client\Flow\CachedTokenFlowInterface;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrantFactoryInterface;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrantInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\RefreshTokenManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(RefreshTokenManager::class)]
final class RefreshTokenManagerTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[TestWith([true])]
    #[TestWith([false])]
    public function testGetAccessTokenRunsTheGrantThroughTheFlow(bool $forceNew): void
    {
        $grant = self::createStub(RefreshTokenGrantInterface::class);
        $accessToken = self::createStub(AccessTokenInterface::class);

        $grantFactory = self::createMock(RefreshTokenGrantFactoryInterface::class);
        $grantFactory->expects(self::once())->method('create')->with('test-client-id')->willReturn($grant);

        $flow = self::createMock(CachedTokenFlowInterface::class);
        $flow->expects(self::once())->method('getAccessToken')->with($grant, $forceNew)->willReturn($accessToken);

        self::assertSame($accessToken, (new RefreshTokenManager($flow, $grantFactory))->getAccessToken('test-client-id', $forceNew));
    }
}

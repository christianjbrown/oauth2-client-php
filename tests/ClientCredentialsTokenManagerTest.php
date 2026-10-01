<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests;

use ChristianBrown\OAuth2Client\ClientCredentialsTokenManager;
use ChristianBrown\OAuth2Client\Flow\CachedTokenFlowInterface;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrantFactoryInterface;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrantInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClientCredentialsTokenManager::class)]
final class ClientCredentialsTokenManagerTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[TestWith([true])]
    #[TestWith([false])]
    public function testGetAccessTokenFromBasicAuthRunsTheGrantThroughTheFlow(bool $forceNew): void
    {
        $grant = self::createStub(ClientCredentialsGrantInterface::class);
        $accessToken = self::createStub(AccessTokenInterface::class);

        $grantFactory = self::createMock(ClientCredentialsGrantFactoryInterface::class);
        $grantFactory->expects(self::once())
            ->method('create')
            ->with('test-basic-auth-value', 'test-scope', 'test-client-id')
            ->willReturn($grant);

        $flow = self::createMock(CachedTokenFlowInterface::class);
        $flow->expects(self::once())->method('getAccessToken')->with($grant, $forceNew)->willReturn($accessToken);

        $manager = new ClientCredentialsTokenManager($flow, $grantFactory);

        self::assertSame($accessToken, $manager->getAccessTokenFromBasicAuth('test-basic-auth-value', 'test-scope', 'test-client-id', $forceNew));
    }
}

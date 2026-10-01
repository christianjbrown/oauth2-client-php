<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Grant;

use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\ClientAuthenticationInterface;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifierInterface;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrant;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrantFactory;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\TokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(RefreshTokenGrantFactory::class)]
#[CoversClass(RefreshTokenGrant::class)]
final class RefreshTokenGrantFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateBindsClientIdAndCollaborators(): void
    {
        $accessToken = self::createStub(AccessTokenInterface::class);

        $endpoint = self::createMock(TokenEndpointInterface::class);
        $endpoint->expects(self::once())
            ->method('requestToken')
            ->with(self::anything(), self::callback(static fn (array $body): bool => 'test-client-id' === $body['client_id']))
            ->willReturn($accessToken);

        $authentication = self::createStub(ClientAuthenticationInterface::class);
        $authentication->method('getBodyFields')->willReturn([TokenManagerInterface::REQUEST_KEY_CLIENT_ID => 'test-client-id']);
        $authentication->method('getHeaders')->willReturn([]);

        $factory = new RefreshTokenGrantFactory($endpoint, self::createStub(KeyValueStoreInterface::class), $authentication, self::createStub(InvalidGrantClassifierInterface::class));

        self::assertSame($accessToken, $factory->create('test-client-id')->requestToken());
    }
}

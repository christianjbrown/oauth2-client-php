<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Grant;

use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrant;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrantFactory;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\TokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClientCredentialsGrantFactory::class)]
#[CoversClass(ClientCredentialsGrant::class)]
final class ClientCredentialsGrantFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateBindsInputsToTheGrant(): void
    {
        $accessToken = self::createStub(AccessTokenInterface::class);

        $endpoint = self::createMock(TokenEndpointInterface::class);
        $endpoint->expects(self::once())
            ->method('requestToken')
            ->with(
                self::anything(),
                self::callback(static fn (array $body): bool => 'test-scope' === $body[TokenManagerInterface::REQUEST_KEY_SCOPE] && 'test-client-id' === $body[TokenManagerInterface::REQUEST_KEY_CLIENT_ID]),
            )
            ->willReturn($accessToken);

        $factory = new ClientCredentialsGrantFactory($endpoint);

        self::assertSame($accessToken, $factory->create('test-basic-auth-value', 'test-scope', 'test-client-id')->requestToken());
    }
}

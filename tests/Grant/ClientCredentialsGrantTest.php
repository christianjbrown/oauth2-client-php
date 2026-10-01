<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Grant;

use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrant;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\GrantType;
use ChristianBrown\OAuth2Client\TokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function sprintf;

#[CoversClass(ClientCredentialsGrant::class)]
final class ClientCredentialsGrantTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[TestWith(['test-scope', 'test-client-id'])]
    #[TestWith([null, 'test-client-id'])]
    #[TestWith(['test-scope', null])]
    #[TestWith([null, null])]
    public function testRequestTokenSendsBasicAuthAndOmitsEmptyFields(?string $scope, ?string $clientId): void
    {
        $accessToken = self::createStub(AccessTokenInterface::class);

        $expectedBody = [TokenManagerInterface::REQUEST_KEY_GRANT_TYPE => GrantType::CLIENT_CREDENTIALS->value];
        if (null !== $scope) {
            $expectedBody[TokenManagerInterface::REQUEST_KEY_SCOPE] = $scope;
        }
        if (null !== $clientId) {
            $expectedBody[TokenManagerInterface::REQUEST_KEY_CLIENT_ID] = $clientId;
        }

        $endpoint = self::createMock(TokenEndpointInterface::class);
        $endpoint->expects(self::once())
            ->method('requestToken')
            ->with(
                [
                    TokenManagerInterface::HEADER_KEY_CONTENT_TYPE => TokenManagerInterface::HEADER_VALUE_CONTENT_TYPE_FORM,
                    TokenManagerInterface::HEADER_KEY_AUTHORIZATION => sprintf(TokenManagerInterface::BASIC_AUTH_VALUE_SPRINTF, base64_encode('test-basic-auth-value')),
                ],
                $expectedBody,
            )
            ->willReturn($accessToken);

        $grant = new ClientCredentialsGrant('test-basic-auth-value', $scope, $clientId, $endpoint);

        self::assertSame($accessToken, $grant->requestToken());
    }
}

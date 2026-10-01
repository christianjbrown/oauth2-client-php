<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Grant;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;
use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\ClientAuthenticationInterface;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifierInterface;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrant;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\InvalidGrantException;
use ChristianBrown\OAuth2Client\Model\Exception\InvalidGrantExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestException;
use ChristianBrown\OAuth2Client\Model\GrantType;
use ChristianBrown\OAuth2Client\TokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(RefreshTokenGrant::class)]
#[CoversClass(InvalidGrantException::class)]
#[CoversClass(RequestException::class)]
final class RefreshTokenGrantTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInvalidGrantClearsRefreshTokenAndThrows(): void
    {
        $apiException = self::createStub(ExceptionInterface::class);

        $endpoint = self::createStub(TokenEndpointInterface::class);
        $endpoint->method('requestToken')->willThrowException(new RequestException($apiException));

        $store = self::createMock(KeyValueStoreInterface::class);
        $store->method('getValue')->willReturn('test-old-refresh-token');
        $store->expects(self::once())->method('setValue')->with(null);

        $classifier = self::createMock(InvalidGrantClassifierInterface::class);
        $classifier->expects(self::once())->method('isInvalidGrant')->with($apiException)->willReturn(true);

        $grant = new RefreshTokenGrant('test-client-id', $endpoint, $store, self::createStub(ClientAuthenticationInterface::class), $classifier);

        try {
            $grant->requestToken();
            self::fail('Expected an InvalidGrantException.');
        } catch (InvalidGrantExceptionInterface $e) {
            self::assertSame($apiException, $e->getRequestException());
        }
    }

    /**
     * @throws Exception
     */
    public function testOtherRequestFailuresPropagateAndKeepRefreshToken(): void
    {
        $failure = new RequestException(self::createStub(ExceptionInterface::class));

        $endpoint = self::createStub(TokenEndpointInterface::class);
        $endpoint->method('requestToken')->willThrowException($failure);

        $store = self::createMock(KeyValueStoreInterface::class);
        $store->method('getValue')->willReturn('test-old-refresh-token');
        $store->expects(self::never())->method('setValue');

        $classifier = self::createStub(InvalidGrantClassifierInterface::class);
        $classifier->method('isInvalidGrant')->willReturn(false);

        $grant = new RefreshTokenGrant('test-client-id', $endpoint, $store, self::createStub(ClientAuthenticationInterface::class), $classifier);

        try {
            $grant->requestToken();
            self::fail('Expected a RequestException.');
        } catch (RequestException $e) {
            self::assertSame($failure, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function testRequestTokenSendsStoredRefreshTokenAndStoresRotatedOne(): void
    {
        $accessToken = self::createStub(AccessTokenInterface::class);
        $accessToken->method('getRefreshToken')->willReturn('test-new-refresh-token');

        $endpoint = self::createMock(TokenEndpointInterface::class);
        $endpoint->expects(self::once())
            ->method('requestToken')
            ->with(
                [TokenManagerInterface::HEADER_KEY_CONTENT_TYPE => TokenManagerInterface::HEADER_VALUE_CONTENT_TYPE_FORM, 'auth-header' => 'auth-value'],
                [
                    TokenManagerInterface::REQUEST_KEY_GRANT_TYPE => GrantType::REFRESH_TOKEN->value,
                    TokenManagerInterface::REQUEST_KEY_REFRESH_TOKEN => 'test-old-refresh-token',
                    'auth-field' => 'auth-field-value',
                ],
            )
            ->willReturn($accessToken);

        $store = self::createMock(KeyValueStoreInterface::class);
        $store->method('getValue')->willReturn('test-old-refresh-token');
        $store->expects(self::once())->method('setValue')->with('test-new-refresh-token');

        $authentication = self::createStub(ClientAuthenticationInterface::class);
        $authentication->method('getHeaders')->willReturn(['auth-header' => 'auth-value']);
        $authentication->method('getBodyFields')->willReturn(['auth-field' => 'auth-field-value']);

        $grant = new RefreshTokenGrant('test-client-id', $endpoint, $store, $authentication, self::createStub(InvalidGrantClassifierInterface::class));

        self::assertSame($accessToken, $grant->requestToken());
    }
}

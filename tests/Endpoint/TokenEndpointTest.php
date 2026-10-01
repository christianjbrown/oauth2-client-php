<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Endpoint;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;
use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpoint;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestException;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;
use ChristianBrown\OAuth2Client\Transformer\AccessTokenTransformerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenEndpoint::class)]
#[CoversClass(RequestException::class)]
final class TokenEndpointTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRequestTokenPostsFormAndTransformsResponse(): void
    {
        $sender = self::createMock(JsonApiRequestSenderInterface::class);
        $sender->expects(self::once())
            ->method('postForm')
            ->with('test-url', [], ['h' => 'v'], ['b' => 'v'])
            ->willReturn(['test-data']);

        $accessToken = self::createStub(AccessTokenInterface::class);
        $transformer = self::createMock(AccessTokenTransformerInterface::class);
        $transformer->expects(self::once())
            ->method('transform')
            ->with(['test-data'])
            ->willReturn($accessToken);

        $endpoint = new TokenEndpoint($sender, $transformer, 'test-url');

        self::assertSame($accessToken, $endpoint->requestToken(['h' => 'v'], ['b' => 'v']));
    }

    /**
     * @throws Exception
     */
    public function testRequestTokenWrapsTransportFailures(): void
    {
        $failure = self::createStub(ExceptionInterface::class);
        $sender = self::createStub(JsonApiRequestSenderInterface::class);
        $sender->method('postForm')->willThrowException($failure);

        $transformer = self::createMock(AccessTokenTransformerInterface::class);
        $transformer->expects(self::never())->method('transform');

        $endpoint = new TokenEndpoint($sender, $transformer, 'test-url');

        try {
            $endpoint->requestToken([], []);
            self::fail('Expected a RequestException.');
        } catch (RequestExceptionInterface $e) {
            self::assertSame($failure, $e->getRequestException());
        }
    }
}

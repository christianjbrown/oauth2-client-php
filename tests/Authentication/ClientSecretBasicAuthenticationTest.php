<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Authentication;

use ChristianBrown\OAuth2Client\Authentication\ClientSecretBasicAuthentication;
use ChristianBrown\OAuth2Client\TokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function sprintf;

#[CoversClass(ClientSecretBasicAuthentication::class)]
final class ClientSecretBasicAuthenticationTest extends TestCase
{
    public function testSendsBasicAuthorizationHeaderOnly(): void
    {
        $authentication = new ClientSecretBasicAuthentication('test-secret');

        self::assertSame(
            [TokenManagerInterface::HEADER_KEY_AUTHORIZATION => sprintf(TokenManagerInterface::BASIC_AUTH_VALUE_SPRINTF, base64_encode('test-client-id:test-secret'))],
            $authentication->getHeaders('test-client-id'),
        );
        self::assertSame([], $authentication->getBodyFields('test-client-id'));
    }
}

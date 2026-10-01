<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests\Authentication;

use ChristianBrown\OAuth2Client\Authentication\PublicClientAuthentication;
use ChristianBrown\OAuth2Client\TokenManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PublicClientAuthentication::class)]
final class PublicClientAuthenticationTest extends TestCase
{
    public function testSendsClientIdInBodyOnly(): void
    {
        $authentication = new PublicClientAuthentication();

        self::assertSame([], $authentication->getHeaders('test-client-id'));
        self::assertSame([TokenManagerInterface::REQUEST_KEY_CLIENT_ID => 'test-client-id'], $authentication->getBodyFields('test-client-id'));
    }
}

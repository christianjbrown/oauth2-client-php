<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests;

use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\PublicClientAuthentication;
use ChristianBrown\OAuth2Client\Cache\AccessTokenCache;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpoint;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifier;
use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculator;
use ChristianBrown\OAuth2Client\Flow\CachedTokenFlow;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrant;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrantFactory;
use ChristianBrown\OAuth2Client\Lock\NullLock;
use ChristianBrown\OAuth2Client\Model\AccessToken;
use ChristianBrown\OAuth2Client\RefreshTokenManager;
use ChristianBrown\OAuth2Client\RefreshTokenManagerFactory;
use ChristianBrown\OAuth2Client\Transformer\AccessTokenTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[CoversClass(RefreshTokenManagerFactory::class)]
#[CoversClass(RefreshTokenManager::class)]
#[CoversClass(CachedTokenFlow::class)]
#[CoversClass(AccessTokenCache::class)]
#[CoversClass(TokenExpiryCalculator::class)]
#[CoversClass(TokenEndpoint::class)]
#[CoversClass(AccessTokenTransformer::class)]
#[CoversClass(AccessToken::class)]
#[CoversClass(RefreshTokenGrantFactory::class)]
#[CoversClass(RefreshTokenGrant::class)]
#[CoversClass(InvalidGrantClassifier::class)]
#[CoversClass(NullLock::class)]
#[CoversClass(PublicClientAuthentication::class)]
final class RefreshTokenManagerFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateWiresAWorkingManager(): void
    {
        $sender = self::createMock(JsonApiRequestSenderInterface::class);
        $sender->expects(self::once())
            ->method('postForm')
            ->with('test-url')
            ->willReturn(['access_token' => 'test-access-token', 'expires_in' => 60, 'token_type' => 'Bearer', 'refresh_token' => 'test-new-refresh-token']);

        $accessStore = self::createMock(TtlAwareKeyValueStoreInterface::class);
        $accessStore->method('getValue')->willReturn(null);
        $accessStore->expects(self::once())->method('setValue')->with('test-access-token', 1060);

        $refreshStore = self::createMock(KeyValueStoreInterface::class);
        $refreshStore->method('getValue')->willReturn('test-old-refresh-token');
        $refreshStore->expects(self::once())->method('setValue')->with('test-new-refresh-token');

        $manager = (new RefreshTokenManagerFactory(new MockClock('@1000')))
            ->create($sender, $accessStore, $refreshStore, 'test-url', new PublicClientAuthentication(), new NullLock());

        self::assertSame('test-access-token', $manager->getAccessToken('test-client-id')->getAccessToken());
    }
}

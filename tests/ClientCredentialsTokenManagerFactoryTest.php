<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Tests;

use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Cache\AccessTokenCache;
use ChristianBrown\OAuth2Client\ClientCredentialsTokenManager;
use ChristianBrown\OAuth2Client\ClientCredentialsTokenManagerFactory;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpoint;
use ChristianBrown\OAuth2Client\Expiry\TokenExpiryCalculator;
use ChristianBrown\OAuth2Client\Flow\CachedTokenFlow;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrant;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrantFactory;
use ChristianBrown\OAuth2Client\Lock\NullLock;
use ChristianBrown\OAuth2Client\Model\AccessToken;
use ChristianBrown\OAuth2Client\Transformer\AccessTokenTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

#[CoversClass(ClientCredentialsTokenManagerFactory::class)]
#[CoversClass(ClientCredentialsTokenManager::class)]
#[CoversClass(CachedTokenFlow::class)]
#[CoversClass(AccessTokenCache::class)]
#[CoversClass(TokenExpiryCalculator::class)]
#[CoversClass(TokenEndpoint::class)]
#[CoversClass(AccessTokenTransformer::class)]
#[CoversClass(AccessToken::class)]
#[CoversClass(ClientCredentialsGrantFactory::class)]
#[CoversClass(ClientCredentialsGrant::class)]
#[CoversClass(NullLock::class)]
final class ClientCredentialsTokenManagerFactoryTest extends TestCase
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
            ->willReturn(['access_token' => 'test-access-token', 'expires_in' => 60, 'token_type' => 'Bearer']);

        $accessStore = self::createMock(TtlAwareKeyValueStoreInterface::class);
        $accessStore->method('getValue')->willReturn(null);
        $accessStore->expects(self::once())->method('setValue')->with('test-access-token', 1060);

        $manager = (new ClientCredentialsTokenManagerFactory(new MockClock('@1000')))
            ->create($sender, $accessStore, 'test-url', new NullLock());

        self::assertSame('test-access-token', $manager->getAccessTokenFromBasicAuth('id:secret')->getAccessToken());
    }
}

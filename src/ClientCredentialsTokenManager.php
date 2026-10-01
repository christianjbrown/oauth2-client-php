<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client;

use ChristianBrown\OAuth2Client\Flow\CachedTokenFlowInterface;
use ChristianBrown\OAuth2Client\Grant\ClientCredentialsGrantFactoryInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\BadResponsePayloadFieldExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;

final class ClientCredentialsTokenManager implements ClientCredentialsTokenManagerInterface
{
    private CachedTokenFlowInterface $flow;
    private ClientCredentialsGrantFactoryInterface $grantFactory;

    public function __construct(CachedTokenFlowInterface $flow, ClientCredentialsGrantFactoryInterface $grantFactory)
    {
        $this->flow = $flow;
        $this->grantFactory = $grantFactory;
    }

    /**
     * @throws RequestExceptionInterface
     * @throws BadResponsePayloadFieldExceptionInterface
     */
    public function getAccessTokenFromBasicAuth(string $basicAuthValue, ?string $scope = null, ?string $clientId = null, bool $forceNew = false): AccessTokenInterface
    {
        return $this->flow->getAccessToken($this->grantFactory->create($basicAuthValue, $scope, $clientId), $forceNew);
    }
}

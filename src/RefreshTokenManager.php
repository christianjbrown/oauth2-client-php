<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client;

use ChristianBrown\OAuth2Client\Flow\CachedTokenFlowInterface;
use ChristianBrown\OAuth2Client\Grant\RefreshTokenGrantFactoryInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\BadResponsePayloadFieldExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\InvalidGrantExceptionInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;

final class RefreshTokenManager implements RefreshTokenManagerInterface
{
    private CachedTokenFlowInterface $flow;
    private RefreshTokenGrantFactoryInterface $grantFactory;

    public function __construct(CachedTokenFlowInterface $flow, RefreshTokenGrantFactoryInterface $grantFactory)
    {
        $this->flow = $flow;
        $this->grantFactory = $grantFactory;
    }

    /**
     * @throws RequestExceptionInterface
     * @throws InvalidGrantExceptionInterface
     * @throws BadResponsePayloadFieldExceptionInterface
     */
    public function getAccessToken(string $clientId, bool $forceNew = false): AccessTokenInterface
    {
        return $this->flow->getAccessToken($this->grantFactory->create($clientId), $forceNew);
    }
}

<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Grant;

use ChristianBrown\KeyValueStore\KeyValueStoreInterface;
use ChristianBrown\OAuth2Client\Authentication\ClientAuthenticationInterface;
use ChristianBrown\OAuth2Client\Endpoint\TokenEndpointInterface;
use ChristianBrown\OAuth2Client\Error\InvalidGrantClassifierInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\InvalidGrantException;
use ChristianBrown\OAuth2Client\Model\Exception\RequestExceptionInterface;
use ChristianBrown\OAuth2Client\Model\GrantType;
use ChristianBrown\OAuth2Client\TokenManagerInterface;

use function array_merge;

final class RefreshTokenGrant implements RefreshTokenGrantInterface
{
    private ClientAuthenticationInterface $clientAuthentication;
    private string $clientId;
    private TokenEndpointInterface $endpoint;
    private InvalidGrantClassifierInterface $invalidGrantClassifier;
    private KeyValueStoreInterface $refreshTokenKeyValueStore;

    public function __construct(string $clientId, TokenEndpointInterface $endpoint, KeyValueStoreInterface $refreshTokenKeyValueStore, ClientAuthenticationInterface $clientAuthentication, InvalidGrantClassifierInterface $invalidGrantClassifier)
    {
        $this->clientAuthentication = $clientAuthentication;
        $this->clientId = $clientId;
        $this->endpoint = $endpoint;
        $this->invalidGrantClassifier = $invalidGrantClassifier;
        $this->refreshTokenKeyValueStore = $refreshTokenKeyValueStore;
    }

    public function requestToken(): AccessTokenInterface
    {
        $headers = array_merge(
            [TokenManagerInterface::HEADER_KEY_CONTENT_TYPE => TokenManagerInterface::HEADER_VALUE_CONTENT_TYPE_FORM],
            $this->clientAuthentication->getHeaders($this->clientId),
        );
        $bodyData = array_merge(
            [
                TokenManagerInterface::REQUEST_KEY_GRANT_TYPE => GrantType::REFRESH_TOKEN->value,
                TokenManagerInterface::REQUEST_KEY_REFRESH_TOKEN => (string) $this->refreshTokenKeyValueStore->getValue(),
            ],
            $this->clientAuthentication->getBodyFields($this->clientId),
        );

        try {
            $accessToken = $this->endpoint->requestToken($headers, $bodyData);
        } catch (RequestExceptionInterface $e) {
            if ($this->invalidGrantClassifier->isInvalidGrant($e->getRequestException())) {
                // The refresh token is dead (revoked/expired): drop it so it is
                // not retried forever, and signal that a fresh authorisation is
                // required rather than a transient request failure.
                $this->refreshTokenKeyValueStore->setValue(null);

                throw new InvalidGrantException($e->getRequestException());
            }

            throw $e;
        }

        $this->storeRotatedRefreshToken($accessToken);

        return $accessToken;
    }

    /**
     * RFC 6749 section 6 lets a refresh response leave the refresh token out,
     * in which case the one already stored stays valid (eBay does this; Etsy
     * rotates on every refresh). Only a token that was actually issued is
     * written, so a server that does not rotate never wipes the stored one.
     */
    private function storeRotatedRefreshToken(AccessTokenInterface $accessToken): void
    {
        $rotated = $accessToken->getRefreshToken();
        if (null === $rotated) {
            return;
        }
        $this->refreshTokenKeyValueStore->setValue($rotated);
    }
}

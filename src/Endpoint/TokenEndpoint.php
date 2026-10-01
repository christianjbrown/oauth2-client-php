<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Endpoint;

use ChristianBrown\ApiClient\Exception\ExceptionInterface;
use ChristianBrown\ApiClient\JsonApiRequestSenderInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\Exception\RequestException;
use ChristianBrown\OAuth2Client\Transformer\AccessTokenTransformerInterface;

final class TokenEndpoint implements TokenEndpointInterface
{
    private JsonApiRequestSenderInterface $apiRequestSender;
    private AccessTokenTransformerInterface $tokenTransformer;
    private string $url;

    public function __construct(JsonApiRequestSenderInterface $apiRequestSender, AccessTokenTransformerInterface $tokenTransformer, string $url)
    {
        $this->apiRequestSender = $apiRequestSender;
        $this->tokenTransformer = $tokenTransformer;
        $this->url = $url;
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, string> $bodyData
     */
    public function requestToken(array $headers, array $bodyData): AccessTokenInterface
    {
        try {
            $data = $this->apiRequestSender->postForm($this->url, [], $headers, $bodyData);
        } catch (ExceptionInterface $e) {
            throw new RequestException($e);
        }

        return $this->tokenTransformer->transform($data);
    }
}

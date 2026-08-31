<?php

declare(strict_types=1);

namespace ChristianBrown\OAuth2Client\Transformer;

use ChristianBrown\OAuth2Client\Model\AccessTokenInterface;
use ChristianBrown\OAuth2Client\Model\AccessTokenType;
use ChristianBrown\OAuth2Client\Model\Exception\BadResponsePayloadFieldExceptionInterface;

interface AccessTokenTransformerInterface
{
    public const string KEY_ACCESS_TOKEN = 'access_token';
    public const string KEY_EXPIRES_IN = 'expires_in';
    public const string KEY_REFRESH_TOKEN = 'refresh_token';
    public const string KEY_SCOPE = 'scope';
    public const string KEY_TOKEN_TYPE = 'token_type';

    /**
     * Maps a lower-cased `token_type` to its canonical AccessTokenType value.
     *
     * RFC 6749 section 7.1 defines token_type as case-insensitive, so providers
     * such as SmartThings return "bearer". eBay goes further and describes the
     * grant rather than the scheme, returning "Application Access Token" or
     * "User Access Token" for what are, in both cases, bearer tokens.
     *
     * @var array<string, AccessTokenType>
     */
    public const array TOKEN_TYPE_ALIASES = [
        'application access token' => AccessTokenType::BEARER,
        'bearer' => AccessTokenType::BEARER,
        'user access token' => AccessTokenType::BEARER,
    ];

    /**
     * @param array<array-key, mixed> $data
     *
     * @throws BadResponsePayloadFieldExceptionInterface
     */
    public function transform(array $data): AccessTokenInterface;
}

# OAuth2 Client

[![CI](https://github.com/christianjbrown/oauth2-client-php/actions/workflows/ci.yml/badge.svg)](https://github.com/christianjbrown/oauth2-client-php/actions/workflows/ci.yml) [![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/christianjbrown/oauth2-client-php/actions/workflows/ci.yml) [![Packagist](https://img.shields.io/packagist/v/christianjbrown/oauth2-client)](https://packagist.org/packages/christianjbrown/oauth2-client) [![License](https://img.shields.io/packagist/l/christianjbrown/oauth2-client)](https://github.com/christianjbrown/oauth2-client-php/blob/main/LICENSE) [![PHP](https://img.shields.io/packagist/dependency-v/christianjbrown/oauth2-client/php)](https://packagist.org/packages/christianjbrown/oauth2-client)

A small, strongly-typed PHP **OAuth 2.0 client** that fetches and caches access tokens. It hides
the token endpoint behind a couple of token managers, caches the resulting access (and refresh) token
in an interchangeable [key-value store](https://github.com/christianjbrown/key-value-store-php),
and only calls the endpoint again when the cached token is missing, expired, or a refresh is forced.

Two grant types ship today:

- **Refresh token** (`RefreshTokenManager`) — exchanges a stored refresh token for a new access token.
- **Client credentials** (`ClientCredentialsTokenManager`) — exchanges HTTP Basic credentials for an
  access token.

Both return an `AccessTokenInterface` and normalise transport and payload failures into a single
library exception hierarchy, so callers stay decoupled from the underlying HTTP client.



## :heavy_check_mark: Prerequisites

- [Git](https://git-scm.com/)
- [PHP](https://www.php.net/) 8.5 or higher (8.x)
- [Composer](https://getcomposer.org/)

:bulb: If you're on MacOS and have [Homebrew](https://brew.sh/), PHP and Composer will install with `brew install composer`.



## :building_construction: Installation

For your composer-enabled project:

```bash
composer require christianjbrown/oauth2-client
```



## :computer: Usage

Build a manager with its factory. Each factory takes a PSR-20 clock (these examples use
`Symfony\Component\Clock\NativeClock`) and builds the manager from a JSON API request sender (from
[`api-client`](https://github.com/christianjbrown/api-client-php)), one or more key-value
stores for the cached tokens, the token endpoint URL and a lock. Pass `NullLock` when only one
process refreshes tokens at a time.



### :arrows_counterclockwise: Refresh token grant

```php
use ChristianBrown\OAuth2Client\Authentication\PublicClientAuthentication;
use ChristianBrown\OAuth2Client\Lock\NullLock;
use ChristianBrown\OAuth2Client\RefreshTokenManagerFactory;
use Symfony\Component\Clock\NativeClock;

$manager = (new RefreshTokenManagerFactory(new NativeClock()))->create(
    $jsonApiRequestSender,           // ChristianBrown\ApiClient\JsonApiRequestSenderInterface
    $accessTokenStore,               // ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface
    $refreshTokenStore,              // ChristianBrown\KeyValueStore\KeyValueStoreInterface
    'https://example.com/oauth/token',
    new PublicClientAuthentication(), // or new ClientSecretBasicAuthentication('my-client-secret')
    new NullLock(),                  // or your own LockInterface implementation
);

$accessToken = $manager->getAccessToken('my-client-id');

$accessToken->getAccessToken(); // the bearer token string
$accessToken->getExpiresIn();   // seconds until expiry

// Force a refresh even if a valid token is cached:
$accessToken = $manager->getAccessToken('my-client-id', true);
```

The manager returns the cached access token while it is still valid. Otherwise it POSTs the stored
refresh token to the endpoint, caches the new access and refresh tokens, and returns the fresh token.



### :key: Client credentials grant

```php
use ChristianBrown\OAuth2Client\ClientCredentialsTokenManagerFactory;
use ChristianBrown\OAuth2Client\Lock\NullLock;
use Symfony\Component\Clock\NativeClock;

$manager = (new ClientCredentialsTokenManagerFactory(new NativeClock()))->create(
    $jsonApiRequestSender, // ChristianBrown\ApiClient\JsonApiRequestSenderInterface
    $accessTokenStore,     // ChristianBrown\KeyValueStore\TtlAwareKeyValueStoreInterface
    'https://example.com/oauth/token',
    new NullLock(),
);

// The Basic auth value is the raw "client_id:client_secret"; the manager base64-encodes it.
$accessToken = $manager->getAccessTokenFromBasicAuth(
    'my-client-id:my-client-secret',
    'my-scope',   // optional
    'my-client-id', // optional
);

$accessToken->getAccessToken();
```


### :arrow_up: Upgrading to 2.0

The manager constructors now take their collaborators, so build managers with the factories. `AccessTokenTransformer`
and `LockInterface` are unchanged.

```php
// Before
$manager = new RefreshTokenManager($sender, $accessStore, $refreshStore, new AccessTokenTransformer(), $url, $clientSecret, $lock);
$manager = new ClientCredentialsTokenManager($sender, $accessStore, new AccessTokenTransformer(), $url);

// After
$factory = new RefreshTokenManagerFactory(new NativeClock());
$manager = $factory->create($sender, $accessStore, $refreshStore, $url, new ClientSecretBasicAuthentication($clientSecret), $lock);
$manager = (new ClientCredentialsTokenManagerFactory(new NativeClock()))->create($sender, $accessStore, $url, new NullLock());
```

Without a client secret use `new PublicClientAuthentication()`; without a lock use `new NullLock()`.



### :ticket: The access token

Every manager returns a `ChristianBrown\OAuth2Client\Model\AccessTokenInterface`:

```php
public function getAccessToken(): string;
public function getExpiresIn(): int;
public function getRefreshToken(): ?string;
public function getScope(): ?string;
public function getTokenType(): AccessTokenType; // enum, currently AccessTokenType::BEARER
```



## :rotating_light: Error handling

Everything the library throws implements
`ChristianBrown\OAuth2Client\Model\Exception\ExceptionInterface` (which extends `Throwable`):

- **`RequestExceptionInterface`** — the token endpoint request failed. The original
  `api-client` exception is available via `getRequestException()`.
- **`BadResponsePayloadFieldExceptionInterface`** — the endpoint responded, but a field was missing,
  the wrong type, or an unsupported value. `getField()` and `getData()` expose the offending field and
  the full payload.

```php
use ChristianBrown\OAuth2Client\Model\Exception\ExceptionInterface;

try {
    $accessToken = $manager->getAccessToken('my-client-id');
} catch (ExceptionInterface $e) {
    print $e->getMessage();
}
```



## :memo: Changelog

Notable changes in each release are listed in [CHANGELOG.md](CHANGELOG.md).



## :page_facing_up: License

Released under the [MIT License](LICENSE).

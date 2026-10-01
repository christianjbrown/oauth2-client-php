# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-10-01

### Added

- `RefreshTokenManagerFactory` and `ClientCredentialsTokenManagerFactory` (each with an interface), which build a
  manager with its default wiring in one call. They take a PSR-20 clock.
- `ClientAuthenticationInterface` with `PublicClientAuthentication` (sends `client_id` in the body) and
  `ClientSecretBasicAuthentication` (HTTP Basic), replacing the optional client secret.
- `Lock\NullLock`, a lock that never blocks, for use where `RefreshTokenManager` previously ran without a lock.
- Small collaborators the managers are now built from: `CachedTokenFlow` (the cache, lock, request and store flow
  both grants share), `TokenGrantInterface` with `RefreshTokenGrant` and `ClientCredentialsGrant` (the
  grant-specific request), `AccessTokenCache`, `TokenExpiryCalculator`, `InvalidGrantClassifier` and `TokenEndpoint`.
  A new grant type is a new `TokenGrantInterface` implementation.
- Requires `psr/clock` and `symfony/clock`.

### Changed

- Allows `christianjbrown/key-value-store` 3.0 as well as 1.x and 2.x. The interfaces this package uses did not change.
- **Breaking:** `RefreshTokenManager::__construct()` now takes `(CachedTokenFlowInterface $flow,
  RefreshTokenGrantFactoryInterface $grantFactory)`. Use `RefreshTokenManagerFactory::create()` for the old
  one-call construction. The lock is now required there (pass `new NullLock()` for none) and the client
  secret is replaced by a `ClientAuthenticationInterface`.
- **Breaking:** `ClientCredentialsTokenManager::__construct()` now takes `(CachedTokenFlowInterface $flow,
  ClientCredentialsGrantFactoryInterface $grantFactory)`. Use `ClientCredentialsTokenManagerFactory::create()`.
- Token expiry is read from the injected clock each time it is needed instead of the global `time()`.
  The expiry is now computed when the token is stored, after the request returns, rather than before it is sent.

## [1.0.1] - 2026-09-30

### Changed

- Allows christianjbrown/key-value-store 2.0 as well as 1.x. Nothing this package uses from it changed.

## [1.0.0] - 2026-09-28

First stable release.

### Added

- `RefreshTokenManager`, which exchanges a stored refresh token for a new access token and caches both
  in key-value stores. The access-token store is a `TtlAwareKeyValueStoreInterface` and the refresh-token
  store a plain `KeyValueStoreInterface`. It returns the cached access token while it is still valid, and
  `getAccessToken()` takes a flag to force a refresh.
- `ClientCredentialsTokenManager`, which exchanges HTTP Basic credentials for an access token and caches it
  in a `TtlAwareKeyValueStoreInterface`.
- `AccessTokenInterface`, returned by both managers, with the access token, seconds until expiry, refresh
  token, scope and an `AccessTokenType` enum. On a cache hit `getExpiresIn()` is the remaining lifetime.
- An optional client secret on `RefreshTokenManager`. When given, the refresh request authenticates with
  HTTP Basic instead of sending `client_id` in the body, which confidential clients require.
- An optional `LockInterface` on `RefreshTokenManager`. When given, a refresh takes the lock and re-reads
  the cache first, so several processes do not all refresh the same token at once.
- A single exception hierarchy under `ExceptionInterface`: request failures (with the original
  `api-client` exception available), bad payload fields, and `InvalidGrantException`, which is thrown
  after the dead refresh token has been cleared from its store.
- `token_type` matching that ignores case and accepts the aliases in `TOKEN_TYPE_ALIASES`, including
  eBay's "Application Access Token" and "User Access Token", which are bearer tokens.

[Unreleased]: https://github.com/christianjbrown/oauth2-client-php/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/christianjbrown/oauth2-client-php/compare/v1.0.1...v2.0.0
[1.0.1]: https://github.com/christianjbrown/oauth2-client-php/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/christianjbrown/oauth2-client-php/releases/tag/v1.0.0

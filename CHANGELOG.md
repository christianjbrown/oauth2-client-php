# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/christianjbrown/oauth2-client-php/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/christianjbrown/oauth2-client-php/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/christianjbrown/oauth2-client-php/releases/tag/v1.0.0

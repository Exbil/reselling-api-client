# Changelog

All notable changes to `exbil/reselling-api-client` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2026-06-02

### Added
- **Cloud Services module** (new): `cloudServices()` handler for container
  based services.
  - Listing, details (with live status), creation, deletion, reinstall,
    status and console command.
  - `cloudServices()->power()` sub-handler: `start()`, `stop()`, `restart()`,
    `kill()` (single power endpoint with an `action` payload).
  - `cloudServices()->files()` sub-handler: list, read, write, upload,
    download, delete, compress, decompress.
  - `cloudServices()->backups()` sub-handler: list, create, delete, restore,
    download. Services and backups are addressed by UUID.
- **Game Server module**: `gameServer()` handler for servers, files, backups,
  console, schedules, databases and eggs.
- **Domain**: `Domain\Domain::checkBulkAvailability(array $domains)` for
  checking the availability of up to 50 domains in a single request.
- **Root Server**: `rootServer()->location()->get(int|string $location)` to
  fetch a single location (by id or slug) including its clusters, and
  `rootServer()->cluster()->getOsListAll()` for the global OS list.

### Changed
- **Root Server** endpoints were aligned with the portal's REST layout. The
  public client method names are unchanged, only the underlying routes moved:
  - Power actions: `/{server}/power/{action}` → `/{server}/{action}`
    (`start`, `stop`, `restart`, `kill`).
  - Resize: `PUT /{server}` → `POST /{server}/resize`.
  - Delete: `DELETE /{server}` → `POST /{server}/delete`.
  - Stats: `/{server}/stats` → `/{server}/live-stats`.
  - Cluster catalogue: `/clusters/{cluster}/os-list` → `/cluster/{cluster}/os-list`,
    `/prices` → `/pricing`, `/price-calc` → `/calculator`.

## [2.0.0] - 2026-04-10

This release is a complete rewrite that aligns the package with the new
Exbil Reselling Portal API. The package was renamed from "Cloud API Client"
to "Reselling API Client" and the namespace changed accordingly.

### Breaking changes
- **Namespace** renamed from `Exbil\CloudApi` to `Exbil\ResellingAPI`. Update
  all imports when upgrading.
- **RootServer** power actions are no longer flat methods on the handler;
  use the dedicated `rootServer()->power()` sub-handler:
  `start()`, `stop()`, `reboot()`, `forceStop()`.
- **RootServer** infrastructure helpers moved to dedicated sub-handlers:
  `rootServer()->location()` for datacenters and
  `rootServer()->cluster()` for cluster, OS list and pricing methods.
- **Mailcow** mailbox / alias / domain admin endpoints moved to dedicated
  sub-handlers: `mailcow()->mailbox()`, `mailcow()->alias()`,
  `mailcow()->domainAdmin()`.
- **VPN** account endpoints moved to `vpn()->account()` sub-handler;
  configuration downloads moved to `vpn()->config()` sub-handler.

### Added
- **Domain module** (new)
  - Registration, transfer, sync, deletion / un-deletion, authcode
  - Handle management (`Domain\Handle`) with type listing and default flag
  - DNS records and zones management (`Domain\DNS`) including bulk update
  - Nameserver management (`Domain\Nameserver`)
  - Pricing per TLD (`Domain\Pricing`)
- **Key validation** endpoint via `Client::validateKey()` returning the
  scoped permissions and rate-limit info for the configured key.
- **VPN** account `enable()` / `disable()` / `sync()` and
  `changePassword()` methods.
- **VPN** GeoIP lookup via `vpn()->getGeoIP()`.
- **Username availability** check via `vpn()->checkUsername()`.
- **Server diagnostics**: `getStats()`, `getLogs()` and `getTasks()` on the
  `RootServer` handler.
- **CHANGELOG.md** documenting future releases.

### Changed
- `User-Agent` bumped to `ExbilResellingApiClient/2.0`.
- `composer.json` description updated to reflect the rebrand.
- README rewritten with the new module hierarchy and full method tables.
- Exception hierarchy keeps the same names but lives under
  `Exbil\ResellingAPI\Exceptions`.

## [1.0.0] - 2025-01-14

Initial release as `Exbil\CloudApi`.

### Added
- HTTP client based on Guzzle 7.9
- Bearer token authentication
- Handlers: `Accounting`, `RootServer`, `VPN`, `Mailcow`
- Exception hierarchy with HTTP status mapping

# Changelog

All notable changes to `exbil/reselling-api-client` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **Cloud Services → allocations($uuid)**: every port the
  server has, zipped 1:1 with the cloud-service `port_schema` so
  the response already carries role + protocol + description per
  entry. Use this instead of `get($uuid)` to render a Network view
  for multi-port services (TS3 voice + query + filetransfer, Source
  engines with GoTV, FiveM with txAdmin, ...).
- **Cloud Services → consoleToken($uuid)**: short-lived scoped
  token + ready-to-use wss:// URLs for the live console + stats
  streams. Pass the returned `subprotocols` array as the
  `Sec-WebSocket-Protocol` header on the WebSocket connection — the
  daemon authenticates from there, so the token never lands in proxy
  logs or browser history. Token TTL ~5 min; re-call to renew.
- **Cloud Services → Network**: per-server IPv6 lifecycle.
  `network()->status($uuid)` reports whether the node operator's
  prefix is configured and whether upstream transit is healthy;
  `network()->listIpv6($uuid)`, `orderIpv6($uuid)` and
  `releaseIpv6($uuid, $id)` manage up to four addresses per server out
  of the routed prefix. The daemon returns a structured
  `ipv6_transit_down` body on HTTP 503 — surface
  `detail.last_checked_at` + `detail.last_error` rather than retrying
  blindly.

## [1.0.0] - 2026-06-02

Initial public release of the Exbil Reselling Portal API client.

### Added
- HTTP client based on Guzzle 7.9 with Bearer token authentication and a
  typed exception hierarchy mapped to HTTP status codes.
- `Client::validateKey()` returning the scoped permissions and rate-limit
  budget for the configured key.
- **Accounting**: billing, invoices, credit status and usage.
- **Domain**: registration, transfer, sync, deletion / un-deletion, authcode,
  handles, DNS records & zones (including bulk update), nameservers, per-TLD
  pricing and bulk availability checks.
- **Root Server**: listing, details, creation, resize, delete, reinstall,
  reset root password, live stats, logs and tasks; with `power()`,
  `location()` and `cluster()` sub-handlers (locations with nested clusters,
  OS lists and pricing / calculator).
- **Cloud Services**: container based services addressed by UUID, with
  `power()`, `files()` and `backups()` sub-handlers plus status, reinstall
  and console command.
- **Game Server**: servers, files, backups, console, schedules, databases
  and eggs.
- **VPN**: accounts, configurations, servers, ports, pricing, GeoIP and
  username availability.
- **Mailcow**: domains, mailboxes, aliases and domain admins.

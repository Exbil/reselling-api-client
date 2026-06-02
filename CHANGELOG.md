# Changelog

All notable changes to `exbil/reselling-api-client` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

# Changelog

All notable changes to `exbil/reselling-api-client` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.0] - 2026-07-31

### Added
- **`metrics($uuid, $range = '24h')`** — the recorded resource history: CPU,
  memory, disk, network and disk I/O over `1h`, `24h`, `7d` or `30d`.

  `status()` returns one instantaneous reading, so charting it only ever shows
  what you observed while your own process was running — a fresh page starts
  with an empty graph. These samples are collected server-side and outlive the
  caller, which is what makes a "last 24 hours" view possible.

  Points are bucketed so the series stays drawable (a month at raw resolution
  is tens of thousands of rows). `bucket` is the slot width in seconds; `ts` is
  milliseconds, ready for a JavaScript `Date`. Gauges are averaged across a
  bucket, cumulative counters take the maximum. An unrecognised range falls
  back to the default instead of failing, and the response echoes both the
  range applied and `available_ranges`.

## [2.1.0] - 2026-07-30

### Added
- **`setRootPassword($uuid, $password)`** — change the container's root
  password. This is the credential used for SSH, SFTP and the web console;
  the panel stores the new value only after the container has accepted it, so
  a failed call leaves the previous password valid.
- **`setDomain($uuid, $domain)`** — point a domain at a service, or pass
  `null` to clear it. A node shares one public IPv4 between all its
  containers, so a web service is only reachable by name once the edge proxy
  knows which hostname belongs to which container; this saves the domain and
  republishes the proxy in one step. Until a domain is set, the service is
  still reachable on its forwarded port — see `allocations()`.
- Service payloads now include `primary_domain`, `internal_ipv4`,
  `public_ipv6` and `ipv6_count`. `allocation_ip` / `allocation_port` are the
  *published* endpoint (the node's public address and the forwarded port),
  not the container's private address as before.

### Changed
- **`consoleToken()`** takes an optional `$type` — `'serial'` for the xterm
  TTY (default, unchanged) or `'vnc'` for the graphical console.
- **`cpu_limit` is a whole core count**, not a percentage of one core. Where
  you sent `200` for two cores, send `2`. This applies to `create()` and
  `updateResources()`, and is what `get()` and `getAll()` return. Pricing is
  unchanged — the per-core rate was always the unit being charged, the panel
  merely divided by 100 on the way in.
- **`consoleToken()`** returns a single-use console URL instead of a
  WebSocket token: `{kind: "url", url, expires_in_sec}`. The previous
  `token`, `subprotocols`, `websocket_url` and `stats_websocket_url` keys no
  longer exist. Cloud Services run as Proxmox LXC containers, whose console
  is an interactive TTY rather than a log stream, so the session is opened by
  following the URL (browser tab or iframe).
- **`sendCommand()`** answers 501 on container-backed services. There is no
  single foreground process to pipe stdin into; use the console session or
  SSH into the container instead.
- **`network()`** — the per-service IPv6 ordering flow only applies to
  backends that advertise it. A container is given its address when it is
  created, so `status()` reports `enabled: false` and the order/release calls
  answer 501. Check `status()` before showing the CTA.
- **`reinstall()`** no longer takes `auto_start`, and is more destructive than
  it was: the container is destroyed and the template's master cloned again,
  so the service returns on a new VMID with new addresses and a new root
  password.
- **`create()`** no longer accepts a `docker_image` override — templates are
  container images no more.

### Removed
- **Container Registries**: `$client->cloudServices()->registries()` and the
  whole `CloudServices\Registries` class. The registry is no longer a product
  of its own — it is an ordinary Cloud Services template now (one Zot
  container per customer instead of a shared namespace), so it is ordered
  through `cloudServices()->create()` with `template_slug: "lxc-registry"`
  like any other service. The `/products/cloudservices/registries/*`
  endpoints are gone from the API.

## [1.1.0] - 2026-06-25

### Added
- **TeamSpeak**: full support for the TeamSpeak product via
  `$client->teamSpeak()`. Servers (`getPricing`, `getAll`, `get`, `order`/
  `create`, `delete`, `view`, `extras`), power (`start`, `stop`), settings
  (`resize`, `updateSettings`, `broadcast`) plus sub-handlers
  `channels()` (create/delete), `clients()` (move/kick/ban/details),
  `security()` (createToken/deleteToken/addBan/removeBan) and `backups()`
  (download/restore snapshots).
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

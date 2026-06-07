# Exbil Reselling API Client

PHP API Client for the Exbil Reselling Portal.

## Installation

```bash
composer require exbil/reselling-api-client
```

## Quick Start

```php
<?php
require 'vendor/autoload.php';

use Exbil\ResellingAPI\Client;

$client = new Client('your-api-key', 'https://reselling-portal.de/api/');

// List servers
$servers = $client->rootServer()->getAll();

// Create VPN account
$account = $client->vpn()->account()->create('username', 'password');

// Register a domain
$domain = $client->domain()->register('example.com', [
    'owner_handle' => 1,
    'admin_handle' => 1,
    'tech_handle' => 1,
]);

// Create Mailcow domain
$mailcowDomain = $client->mailcow()->create('node-1', [
    'domain' => 'example.com',
    'mailboxes' => 10,
    'aliases' => 50,
    'quota_mb' => 5000,
]);
```

## Authentication

All API requests require an API key transmitted as Bearer Token:

```php
$client = new Client('your-api-key');
```

For the sandbox environment use the sandbox base URL:

```php
$client = new Client('rs_sb_…', 'https://www.reselling-portal.de/api/sandbox/');
```

You can verify the configured key and inspect its scoped permissions
at any time:

```php
$info = $client->validateKey();
// → ['key' => [...], 'permissions' => [...], 'rate_limit' => [...]]
```

---

## API Reference

### Accounting

Billing, invoices, credit and usage.

| Method | Description |
|--------|-------------|
| `getUserData()` | Team/user billing information |
| `getCreditStatus()` | Current credit status |
| `getUsage()` | Current month usage summary |
| `getUsageDetails(array $filters)` | Detailed usage records |
| `getInvoices()` | All invoices |
| `getInvoice(int $id)` | Single invoice |

```php
// Get credit status
$credit = $client->accounting()->getCreditStatus();

// Get usage with filters
$usage = $client->accounting()->getUsageDetails([
    'start' => '2024-01-01',
    'end' => '2024-01-31',
    'product_type' => 'rootserver',
    'limit' => 100,
]);

// Get invoice
$invoice = $client->accounting()->getInvoice(123);
```

---

### Domain

Domain registration, transfer, DNS management and handles.

#### Main Methods

| Method | Description |
|--------|-------------|
| `getAll()` | All domains |
| `get(string $domain)` | Single domain |
| `checkAvailability(string $domain)` | Check availability |
| `checkBulkAvailability(array $domains)` | Bulk availability check (max 50) |
| `register(string $domain, array $handles, array $nameservers, int $period)` | Register domain |
| `transfer(string $domain, string $authcode, array $handles, array $nameservers)` | Transfer domain |
| `sync(string $domain)` | Sync from registrar |
| `getAuthcode(string $domain)` | Get authcode |
| `updateHandles(string $domain, array $handles)` | Update handles |
| `requestDeletion(string $domain)` | Request deletion |
| `cancelDeletion(string $domain)` | Cancel deletion |

```php
// Check availability
$check = $client->domain()->checkAvailability('example.com');

// Bulk availability check (up to 50 domains per request)
$bulk = $client->domain()->checkBulkAvailability([
    'example.com',
    'example.de',
    'example.net',
]);

// Register domain
$domain = $client->domain()->register('example.com', [
    'owner_handle' => 1,
    'admin_handle' => 1,
    'tech_handle' => 1,
], ['ns1.example.net', 'ns2.example.net'], 1);

// Transfer domain
$transfer = $client->domain()->transfer('example.com', 'AUTH-CODE-123', [
    'owner_handle' => 1,
    'admin_handle' => 1,
    'tech_handle' => 1,
]);
```

#### Pricing (`$client->domain()->pricing()`)

| Method | Description |
|--------|-------------|
| `getAll()` | All domain prices |
| `getTlds()` | Available TLDs |
| `getByTld(string $tld)` | Pricing for specific TLD |

```php
$prices = $client->domain()->pricing()->getAll();
$tlds = $client->domain()->pricing()->getTlds();
$comPrice = $client->domain()->pricing()->getByTld('com');
```

#### Handle (`$client->domain()->handle()`)

| Method | Description |
|--------|-------------|
| `getTypes()` | Available handle types |
| `getAll()` | All handles |
| `get(string\|int $id)` | Single handle |
| `create(array $data)` | Create handle |
| `update(string\|int $id, array $data)` | Update handle |
| `delete(string\|int $id)` | Delete handle |
| `setDefault(string\|int $id)` | Set as default |

```php
// Create handle
$handle = $client->domain()->handle()->create([
    'type' => 'person',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'street' => '123 Main St',
    'city' => 'Berlin',
    'zip' => '10115',
    'country' => 'DE',
    'phone' => '+49.301234567',
    'email' => 'john@example.com',
]);

// Set as default
$client->domain()->handle()->setDefault(1);
```

#### Nameserver (`$client->domain()->nameserver()`)

| Method | Description |
|--------|-------------|
| `get(string $domain)` | Get nameservers |
| `update(string $domain, array $nameservers)` | Update nameservers |

```php
$ns = $client->domain()->nameserver()->get('example.com');
$client->domain()->nameserver()->update('example.com', [
    'ns1.example.net',
    'ns2.example.net',
]);
```

#### DNS (`$client->domain()->dns()`)

| Method | Description |
|--------|-------------|
| `get(string $domain)` | Get DNS records |
| `create(string $domain, array $record)` | Create record |
| `update(string $domain, string\|int $recordId, array $data)` | Update record |
| `bulkUpdate(string $domain, array $records)` | Bulk update records |
| `delete(string $domain, string\|int $recordId)` | Delete record |
| `getZones(string $domain)` | Get DNS zones |
| `createZone(string $domain, array $zone)` | Create zone |
| `updateZone(string $domain, string\|int $zoneId, array $data)` | Update zone |
| `deleteZone(string $domain, string\|int $zoneId)` | Delete zone |

```php
// Get DNS records
$records = $client->domain()->dns()->get('example.com');

// Create A record
$record = $client->domain()->dns()->create('example.com', [
    'type' => 'A',
    'name' => '@',
    'content' => '192.168.1.1',
    'ttl' => 3600,
]);

// Create MX record
$client->domain()->dns()->create('example.com', [
    'type' => 'MX',
    'name' => '@',
    'content' => 'mail.example.com',
    'priority' => 10,
]);

// Bulk update
$client->domain()->dns()->bulkUpdate('example.com', [
    ['type' => 'A', 'name' => '@', 'content' => '192.168.1.1'],
    ['type' => 'AAAA', 'name' => '@', 'content' => '2001:db8::1'],
]);
```

---

### Root Server

Manage virtual servers.

#### Main Methods

| Method | Description |
|--------|-------------|
| `getAll(array $filters)` | All servers (filters: state, datacenter_id, cluster_id) |
| `get(int $vmId)` | Single server |
| `create(string $clusterSlug, array $config)` | Create server |
| `update(int $vmId, array $config)` | Resize server |
| `delete(int $vmId)` | Delete server |
| `resetRootPassword(int $vmId, ?string $password)` | Reset root password |
| `reinstall(int $vmId, array $config)` | Reinstall server |
| `getStats(int $vmId)` | Live statistics (CPU, RAM, network) |
| `getLogs(int $vmId, int $limit)` | Server logs |
| `getTasks(int $vmId, int $limit)` | Running/completed tasks |

```php
// Create server
$server = $client->rootServer()->create('cluster-de-1', [
    'hostname' => 'web-server-01',
    'cores' => 4,
    'ram_mb' => 8192,
    'disk_gb' => 100,
    'operating_system_slug' => 'ubuntu-22.04',
    'root_password' => 'secure-password',
    'ipv4_addresses' => 1,
    'backup_slots' => 1,
]);

// Resize server (disk can only be increased)
$client->rootServer()->update(12345, [
    'cores' => 8,
    'ram_mb' => 16384,
]);

// Get stats
$stats = $client->rootServer()->getStats(12345);
```

#### Location (`$client->rootServer()->location()`)

| Method | Description |
|--------|-------------|
| `getAll()` | All datacenters |
| `getClusters(string $datacenterSlug)` | Clusters of a datacenter |

```php
$locations = $client->rootServer()->location()->getAll();
$clusters = $client->rootServer()->location()->getClusters('de-fra');
```

#### Cluster (`$client->rootServer()->cluster()`)

| Method | Description |
|--------|-------------|
| `getAll()` | All clusters |
| `get(string $clusterSlug)` | Single cluster |
| `getOsList(string $clusterSlug)` | Available operating systems |
| `getPrices(string $clusterSlug)` | Price list of a cluster |
| `calculatePrice(string $clusterSlug, array $config)` | Calculate price |

```php
// Get OS list for cluster
$osList = $client->rootServer()->cluster()->getOsList('cluster-de-1');

// Calculate price
$price = $client->rootServer()->cluster()->calculatePrice('cluster-de-1', [
    'cores' => 4,
    'ram_mb' => 8192,
    'disk_gb' => 100,
    'backup_slots' => 1,
    'ipv4_addresses' => 1,
    'ipv6_addresses' => 1,
]);
```

#### Power (`$client->rootServer()->power()`)

| Method | Description |
|--------|-------------|
| `start(int $vmId)` | Start server |
| `stop(int $vmId)` | Shutdown server (graceful) |
| `reboot(int $vmId)` | Reboot server |
| `forceStop(int $vmId)` | Power off server (force) |

```php
$client->rootServer()->power()->start(12345);
$client->rootServer()->power()->stop(12345);
$client->rootServer()->power()->reboot(12345);
$client->rootServer()->power()->forceStop(12345);
```

---

### VPN

VPN accounts and configurations.

#### Main Methods

| Method | Description |
|--------|-------------|
| `getServers()` | All VPN servers |
| `getPorts()` | Available ports |
| `getPricing()` | Pricing |
| `getGeoIP()` | GeoIP info of current request |
| `checkUsername(string $username)` | Check username availability |

```php
$servers = $client->vpn()->getServers();
$pricing = $client->vpn()->getPricing();
$available = $client->vpn()->checkUsername('new-user');
```

#### Account (`$client->vpn()->account()`)

| Method | Description |
|--------|-------------|
| `getAll()` | All VPN accounts |
| `get(int $id)` | Single account |
| `create(string $username, ?string $password)` | Create account |
| `delete(int $id)` | Delete account |
| `sync(int $id)` | Sync account |
| `changePassword(int $id, string $password)` | Change password |
| `enable(int $id)` | Enable account |
| `disable(int $id)` | Disable account |

```php
// Create account
$account = $client->vpn()->account()->create('new-user', 'secure-password');

// Change password
$client->vpn()->account()->changePassword(123, 'new-password');

// Sync account
$client->vpn()->account()->sync(123);

// Enable account (must be disabled, pending, or error state)
$client->vpn()->account()->enable(123);

// Disable account (must be active state)
$client->vpn()->account()->disable(123);
```

> **Note:** Suspended accounts cannot be enabled via API. Contact support to lift the suspension.

#### Config (`$client->vpn()->config()`)

| Method | Description |
|--------|-------------|
| `getOpenVpn(int $accountId, int $serverId, int $portId)` | OpenVPN config (JSON) |
| `downloadOpenVpn(int $accountId, int $serverId, int $portId)` | OpenVPN .ovpn download |
| `getWireGuard(int $accountId, int $serverId)` | WireGuard config (JSON) |
| `downloadWireGuard(int $accountId, int $serverId)` | WireGuard .conf download |

```php
// OpenVPN configuration
$ovpnConfig = $client->vpn()->config()->getOpenVpn(123, 1, 443);

// WireGuard configuration
$wgConfig = $client->vpn()->config()->getWireGuard(123, 1);
```

---

### Mailcow

Email domains, mailboxes and aliases.

#### Main Methods

| Method | Description |
|--------|-------------|
| `getNodes(?string $datacenter)` | All Mailcow nodes |
| `getLoadBalancerStats(?string $datacenter)` | Load balancer statistics |
| `calculatePrice(string $nodeOrDc, int $mailboxes, int $aliases, int $quotaMb)` | Calculate price |
| `getAll(?string $id)` | All or single domain |
| `get(string\|int $id)` | Single domain |
| `create(string $nodeOrDc, array $config)` | Create domain |
| `update(string\|int $id, array $config)` | Update domain |
| `delete(string\|int $id)` | Delete domain |

```php
// Get nodes
$nodes = $client->mailcow()->getNodes();

// Create domain
$domain = $client->mailcow()->create('node-1', [
    'domain' => 'example.com',
    'mailboxes' => 10,
    'aliases' => 50,
    'quota_mb' => 5000,
    'defquota_mb' => 500,
    'maxquota_mb' => 1000,
    'admin_username' => 'admin',
    'admin_password' => 'secure-password',
]);

// Update domain
$client->mailcow()->update('example.com', [
    'mailboxes' => 20,
    'quota_mb' => 10000,
]);
```

#### Mailbox (`$client->mailcow()->mailbox()`)

| Method | Description |
|--------|-------------|
| `getAll(string $domain, ?int $id)` | Get mailboxes |
| `get(string $domain, int $mailboxId)` | Single mailbox |
| `create(string $domain, string $address, array $config)` | Create mailbox |
| `update(string $domain, string $address, array $config)` | Update mailbox |
| `delete(string $domain, string $localPart)` | Delete mailbox |

```php
// Create mailbox
$mailbox = $client->mailcow()->mailbox()->create('example.com', 'info', [
    'password' => 'secure-password',
    'name' => 'Info Mailbox',
    'quota_mb' => 500,
]);

// Update mailbox
$client->mailcow()->mailbox()->update('example.com', 'info', [
    'quota_mb' => 1000,
    'active' => true,
]);

// Delete mailbox
$client->mailcow()->mailbox()->delete('example.com', 'info');
```

#### Alias (`$client->mailcow()->alias()`)

| Method | Description |
|--------|-------------|
| `getAll(string $domain, ?int $id)` | Get aliases |
| `get(string $domain, int $aliasId)` | Single alias |
| `create(string $domain, string $address, array $goto)` | Create alias |
| `update(string $domain, string $address, array $goto, ?bool $active)` | Update alias |
| `delete(string $domain, string $localPart)` | Delete alias |

```php
// Create alias
$alias = $client->mailcow()->alias()->create('example.com', 'support', [
    'info@example.com',
    'admin@example.com',
]);

// Update alias
$client->mailcow()->alias()->update('example.com', 'support', [
    'info@example.com',
], true);

// Delete alias
$client->mailcow()->alias()->delete('example.com', 'support');
```

#### Domain Admin (`$client->mailcow()->domainAdmin()`)

| Method | Description |
|--------|-------------|
| `getAll(string $domain, ?int $id)` | Get domain admins |
| `get(string $domain, int $adminId)` | Single admin |
| `create(string $domain, string $username, ?string $password)` | Create admin |
| `update(string $domain, string $username, array $config)` | Update admin |
| `delete(string $domain, string $username)` | Delete admin |

```php
// Create admin
$admin = $client->mailcow()->domainAdmin()->create('example.com', 'admin', 'secure-password');

// Update admin
$client->mailcow()->domainAdmin()->update('example.com', 'admin', [
    'password' => 'new-password',
    'active' => true,
]);
```

---

### Cloud Services

Container based services — game servers (Minecraft, TeamSpeak, CS2,
Rust, Palworld, …), databases (MySQL, MariaDB, PostgreSQL, MongoDB,
Redis, …), web stacks (Caddy, Apache+PHP, Node.js, Python, Bun, …),
discord bots, and ~250 other templates. Services and backups are
addressed by their UUID.

#### Main Methods

| Method | Description |
|--------|-------------|
| `templates()` | Order catalogue — every active template + node bindings + field_schema |
| `getAll(array $filters = [])` | All cloud services (filters: status, per_page, team_id) |
| `get(string $uuid)` | Service details incl. live status |
| `create(array $config)` | Create a service (node_id, template_slug, name, memory_limit, disk_limit, cpu_limit, …) |
| `delete(string $uuid)` | Delete a service |
| `reinstall(string $uuid, array $options = [])` | Reinstall a service — see options below |
| `status(string $uuid)` | Live resource usage / status |
| `sendCommand(string $uuid, string $command)` | Send a console command (one-shot REST) |
| `consoleToken(string $uuid)` | Issue a scoped token + wss:// URLs for the live console + stats streams |
| `allocations(string $uuid)` | List every port allocation with role + protocol + description |

```php
// === ORDER FLOW ============================================

// 1) Pull the order catalogue
$catalog = $client->cloudServices()->templates();
$mcPaper = collect($catalog['data']['templates'])
    ->firstWhere('slug', 'minecraft-paper');
$node    = $catalog['data']['nodes'][0];

// 2) Place the order
$service = $client->cloudServices()->create([
    'node_id'       => $node['id'],
    'template_slug' => $mcPaper['slug'],
    'name'          => 'My Survival World',
    'memory_limit'  => 4096,                    // MB
    'disk_limit'    => 10240,                   // MB
    'cpu_limit'     => 200,                     // % of one core
    'environment'   => [                        // override env defaults
        'MC_VERSION'    => '1.21.11',
        'RCON_ENABLED'  => 'true',
        'RCON_PASSWORD' => bin2hex(random_bytes(8)),
    ],
]);

// 3) Watch the install land
$uuid = $service['data']['uuid'];
while (true) {
    $s = $client->cloudServices()->get($uuid)['data'];
    if (in_array($s['status'], ['running', 'install_failed', 'offline'])) break;
    sleep(3);
}

// === MANAGEMENT ============================================

$services = $client->cloudServices()->getAll(['status' => 'running']);
$service  = $client->cloudServices()->get('a1b2c3d4-...');
$client->cloudServices()->sendCommand('a1b2c3d4-...', 'say hello');
$client->cloudServices()->allocations('a1b2c3d4-...');
```

**`reinstall($uuid, $options)` options array**

| Key | Type | Description |
|-----|------|-------------|
| `auto_start` | `bool` | When `true`, the daemon starts the server the moment the wipe + image pull finishes — the customer sees the entrypoint's seed phase (Downloading vanilla jar..., npm run setup, ...) in the live console without a second click. **Recommended for customer-facing flows.** |
| `cloudservice` | `string` | Switch to a different service template at the same time (template-switch reinstall). |
| `environment` | `array` | Override environment variables for the (possibly new) template. |

```php
$client->cloudServices()->reinstall('a1b2c3d4-...', ['auto_start' => true]);
```

**`consoleToken($uuid)` — Live WebSocket console + stats**

Use this instead of polling REST `sendCommand()` when you need a live
console view or want to stream output back to a client. The daemon
accepts a short-lived (5 min) scoped token via
`Sec-WebSocket-Protocol`, so the token never lands in URLs / proxy
logs / browser history.

```php
$ws = $client->cloudServices()->consoleToken('a1b2c3d4-...');
// $ws = [
//   'token'               => 'cst_...',
//   'subprotocols'        => ['cst', 'cst_...'],
//   'websocket_url'       => 'wss://node01.example.com:443/api/v1/servers/<uuid>/console',
//   'stats_websocket_url' => 'wss://node01.example.com:443/api/v1/servers/<uuid>/stats',
//   'expires_in_sec'      => 300,
// ]
```

Browser-side (vanilla JS):

```js
const ws = new WebSocket(data.websocket_url, data.subprotocols);
ws.onmessage = e => console.log(e.data);
ws.send(JSON.stringify({event: 'command', command: 'list'}));
```

Token TTL is ~5 min; re-call `consoleToken()` before expiry to keep
a long-running session alive. The same token authenticates BOTH the
console and the stats WebSocket — connect to whichever URL you need.

#### Power (`$client->cloudServices()->power()`)

| Method | Description |
|--------|-------------|
| `start(string $uuid)` | Start service |
| `stop(string $uuid)` | Stop service (graceful) |
| `restart(string $uuid)` | Restart service |
| `kill(string $uuid)` | Kill service (force stop) |

```php
$client->cloudServices()->power()->start('a1b2c3d4-...');
$client->cloudServices()->power()->restart('a1b2c3d4-...');
```

#### Files (`$client->cloudServices()->files()`)

| Method | Description |
|--------|-------------|
| `list(string $uuid, string $dir = '/')` | List a directory |
| `read(string $uuid, string $file)` | Read file contents |
| `write(string $uuid, string $file, string $content)` | Write a file |
| `upload(string $uuid, string $filePath, string $dir = '/')` | Upload a local file |
| `download(string $uuid, string $file)` | Get a download URL |
| `delete(string $uuid, array $files)` | Delete files |
| `compress(string $uuid, array $files, string $output)` | Compress into an archive |
| `decompress(string $uuid, string $file, string $target)` | Extract an archive |

```php
$client->cloudServices()->files()->list('a1b2c3d4-...', '/');
$client->cloudServices()->files()->write('a1b2c3d4-...', 'server.properties', "max-players=20\n");
```

#### Backups (`$client->cloudServices()->backups()`)

Each service has a slot quota (default 3) and a size cap (default
100 GB). Archives are encrypted at rest with AES-256-GCM and tagged
with the team that created or uploaded them — restores across team
boundaries are refused with 403 unless the caller passes
`force => true`.

| Method | Description |
|--------|-------------|
| `getAll(string $uuid)` | All backups for a service (with `team_id`, `cloudservice_id`, `origin`, `is_locked`) |
| `create(string $uuid, ?string $name = null, array $ignoredFiles = [])` | Create a backup of the current volume |
| `upload(string $uuid, string $filePath)` | Upload a `.tar.gz` archive previously downloaded from this platform |
| `delete(string $uuid, string $backupId)` | Delete a backup (sends a deletion-certificate mail to the owner) |
| `restore(string $uuid, string $backupId, array $options = [])` | Restore a backup — stop, extract, chown, auto-start |
| `toggleLock(string $uuid, string $backupId)` | Toggle the lock flag (locked backups skip retention sweeps) |
| `download(string $uuid, string $backupId, string $targetPath)` | Stream the encrypted archive to a local path |

```php
// Snapshot
$client->cloudServices()->backups()->create('a1b2c3d4-...', 'pre-update');

// Download locally (encrypted .tar.gz — keep as-is for re-upload)
$client->cloudServices()->backups()->download(
    'a1b2c3d4-...',
    'backup-uuid-here',
    '/tmp/my-backup.tar.gz',
);

// Re-upload a backup you saved earlier
$client->cloudServices()->backups()->upload(
    'a1b2c3d4-...',
    '/tmp/my-backup.tar.gz',
);

// Restore. switch_template defaults to true — when the archive was
// taken on a different template (TS3 backup on a TS6 server) the
// daemon flips the server's cloudservice_id + docker_image to match
// the backup before extracting. Pass force=true for operator
// recovery (skips both the cross-team and cross-template guards).
$client->cloudServices()->backups()->restore(
    'a1b2c3d4-...',
    'backup-uuid-here',
    ['switch_template' => true],
);

// Lock an archive against the retention sweep
$client->cloudServices()->backups()->toggleLock('a1b2c3d4-...', 'backup-uuid-here');
```

#### Network (`$client->cloudServices()->network()`)

Per-server IPv6 lifecycle. Up to four addresses out of the operator's
routed prefix.

| Method | Description |
|--------|-------------|
| `status(string $uuid)` | Node prefix + upstream healthcheck snapshot |
| `listIpv6(string $uuid)` | Addresses bound to the server (primary first) |
| `orderIpv6(string $uuid)` | Allocate one more (HTTP 503 if transit down) |
| `releaseIpv6(string $uuid, int $addressId)` | Release one by daemon-side id |

```php
$status = $client->cloudServices()->network()->status('a1b2c3d4-...');
if ($status['enabled'] && ($status['healthcheck']['healthy'] ?? false)) {
    $addr = $client->cloudServices()->network()->orderIpv6('a1b2c3d4-...');
    echo $addr['data']['ipv6'];     // e.g. 2a0e:97c0:440:105::abcd
}
```

A 503 response carries a `detail.last_checked_at` + `detail.last_error`
explaining why the upstream is down; surface that to the operator
rather than retrying blindly.

#### Container Registries (`$client->cloudServices()->registries()`)

Customer-owned private Docker registries hosted on the same nodes.
Each registry gets a `<namespace>.<node-domain>` URL and a generated
admin password the customer uses to `docker login` + push images.
Packaged (fixed slug → quotas) or custom (storage / repo / robot
sliders).

| Method | Description |
|--------|-------------|
| `getAll()` | Calling team's registries |
| `get(string $uuid)` | Registry details + admin creds (MANAGE-only) |
| `create(array $config)` | Order a new registry (package or custom mode) |
| `delete(string $uuid)` | Tear down — secure-wipes blobs before releasing the slot |
| `packages()` | Catalogue of available packages with prices |
| `calculatePrice(array $payload)` | Live price preview for the order form |
| `checkNamespace(string $namespace)` | Returns `{available: bool}` for the typed namespace |

```php
// === ORDER FLOW ============================================

// 1) Show catalogue
$packages = $client->cloudServices()->registries()->packages();

// 2) Price-preview
$preview = $client->cloudServices()->registries()->calculatePrice([
    'mode'         => 'package',
    'package_slug' => 'small',
]);

// 3) Reserve the namespace (live as customer types)
$check = $client->cloudServices()->registries()->checkNamespace('myco-prod');
if (!$check['data']['available']) {
    throw new RuntimeException('Namespace taken');
}

// 4) Order
$registry = $client->cloudServices()->registries()->create([
    'mode'         => 'package',
    'package_slug' => 'small',
    'namespace'    => 'myco-prod',
    'auto_upgrade' => true,
]);

// 5) Customer pushes images
//    docker login myco-prod.registry.example.com -u admin -p <admin_password>
//    docker push myco-prod.registry.example.com/myapp:v1.0
```

`packages()` returns slug, included storage / repos / robots, hourly
cents and a marketing blurb so the order form can render the cards
without separate copy decks. Custom mode bypasses the package step:

```php
$client->cloudServices()->registries()->create([
    'mode'        => 'custom',
    'storage_gb'  => 50,
    'repo_limit'  => 20,
    'robot_limit' => 10,
    'namespace'   => 'myco-staging',
]);
```

---

## Error Handling

```php
use Exbil\ResellingAPI\Exceptions\ApiException;
use Exbil\ResellingAPI\Exceptions\AuthenticationException;
use Exbil\ResellingAPI\Exceptions\ForbiddenException;
use Exbil\ResellingAPI\Exceptions\NotFoundException;
use Exbil\ResellingAPI\Exceptions\ValidationException;

try {
    $server = $client->rootServer()->get(99999);
} catch (AuthenticationException $e) {
    // 401 - Invalid API key
    echo "Authentication failed: " . $e->getMessage();
} catch (ForbiddenException $e) {
    // 403 - No permission
    echo "Access denied: " . $e->getMessage();
} catch (NotFoundException $e) {
    // 404 - Resource not found
    echo "Not found: " . $e->getMessage();
} catch (ValidationException $e) {
    // 422 - Validation error
    echo "Validation error: " . $e->getMessage();
    print_r($e->getValidationErrors());
} catch (ApiException $e) {
    // Other API errors
    echo "API error: " . $e->getMessage();
    echo "Status code: " . $e->getCode();
}
```

---

## Asynchronous Operations

Many operations are executed asynchronously and return a 202 status:

- Server create/delete/resize
- Power operations (start, stop, reboot)
- Mailcow domain/mailbox/alias create/update/delete
- VPN account create/delete/enable/disable

The response typically contains a job ID or task information for tracking.

---

## License

BSD-2-Clause

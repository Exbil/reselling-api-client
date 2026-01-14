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

use Exbil\CloudApi\Client;

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

```php
// Create account
$account = $client->vpn()->account()->create('new-user', 'secure-password');

// Change password
$client->vpn()->account()->changePassword(123, 'new-password');

// Sync account
$client->vpn()->account()->sync(123);
```

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

## Error Handling

```php
use Exbil\CloudApi\Exceptions\ApiException;
use Exbil\CloudApi\Exceptions\AuthenticationException;
use Exbil\CloudApi\Exceptions\ForbiddenException;
use Exbil\CloudApi\Exceptions\NotFoundException;
use Exbil\CloudApi\Exceptions\ValidationException;

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
- VPN account create/delete

The response typically contains a job ID or task information for tracking.

---

## License

BSD-2-Clause

<?php

namespace Exbil\ResellingAPI\CloudServices;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class CloudServices
{
    private Client $client;
    private string $basePath = 'v1/products/cloudservices';
    private ?Power $powerHandler = null;
    private ?Files $filesHandler = null;
    private ?Backups $backupsHandler = null;
    private ?Network $networkHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== SERVICE LISTING ====================

    /**
     * Get all cloud services with optional filters.
     *
     * @param array $filters Supported: status, per_page, team_id
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(array $filters = []): array
    {
        return $this->client->get($this->basePath, $filters);
    }

    /**
     * Get a specific cloud service by UUID, including live status from the node.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}");
    }

    /**
     * Catalogue endpoint — every active service template a customer
     * may order, paired with the nodes that have a valid template
     * binding (region-availability + maintenance-mode checked).
     *
     * Use this to render the order form: each entry carries the
     * full `field_schema` (env knobs the customer can override at
     * order time, e.g. SERVER_PORT, MC_VERSION, RCON_PASSWORD), the
     * suggested `environment_defaults`, and the `resource_limits`
     * minimums the panel enforces (memory / disk / cpu floors).
     *
     * Response shape:
     *   {
     *     "templates": [
     *       {
     *         "slug": "lxc-wordpress",
     *         "name": "WordPress",
     *         "category": "applications",
     *         "description": "...",
     *         "environment_defaults": {...},
     *         "field_schema": [...],
     *         "resource_limits": {"min": {...}, "max": {...}},
     *         "node_ids": [1, 2]   ← bind these to the node dropdown
     *       },
     *       ...
     *     ],
     *     "nodes": [
     *       {"id": 1, "name": "fra-01", "location_id": 10,
     *        "maintenance_mode": false},
     *       ...
     *     ]
     *   }
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function templates(): array
    {
        return $this->client->get("{$this->basePath}/templates");
    }

    // ==================== SERVICE CREATION & MANAGEMENT ====================

    /**
     * Create a new cloud service.
     *
     * @param array $config Service configuration. Required: node_id, template_slug,
     *                      name, memory_limit (MB), disk_limit (MB),
     *                      cpu_limit (whole CPU cores). Optional: team_id,
     *                      description, environment.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $config): array
    {
        return $this->client->post($this->basePath, $config);
    }

    /**
     * Delete a cloud service.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $uuid): array
    {
        return $this->client->delete("{$this->basePath}/{$uuid}");
    }

    /**
     * Reinstall a cloud service.
     *
     * @param string $uuid       Service UUID.
     * @param array  $options    Optional overrides:
     *                            - cloudservice (string): switch to a new
     *                              template at the same time.
     *                            - environment (array): override env vars
     *                              for the (possibly new) template.
     *
     *                            A reinstall destroys the container and
     *                            clones the template's master again, so the
     *                            service comes back on a new VMID with new
     *                            addresses and a new root password. All
     *                            customer data is lost — back up first.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reinstall(string $uuid, array $options = []): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/reinstall", $options);
    }

    /**
     * Update memory/disk/cpu allocation for a running service.
     *
     * cpu_limit is a whole core count. A container's disk can only grow —
     * a smaller value is ignored rather than rejected.
     *
     * @param array{memory_limit:int, disk_limit:int, cpu_limit:int} $resources
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateResources(string $uuid, array $resources): array
    {
        return $this->client->patch("{$this->basePath}/{$uuid}/resources", $resources);
    }

    /**
     * Update the customer-facing display name. Pass null/empty to clear back
     * to the product default.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function rename(string $uuid, ?string $name): array
    {
        return $this->client->patch("{$this->basePath}/{$uuid}/name", ['name' => $name]);
    }

    /**
     * Update runtime environment variables.
     *
     * Only keys the template marks user_editable=true are accepted; anything
     * else is dropped before it reaches the node.
     *
     * @param array<string, string> $environment
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateEnvironment(string $uuid, array $environment, bool $restartAfterSave = true): array
    {
        return $this->client->patch("{$this->basePath}/{$uuid}/environment", [
            'environment'        => $environment,
            'restart_after_save' => $restartAfterSave,
        ]);
    }

    /**
     * Set the container's root password.
     *
     * This is the credential the customer uses for SSH, SFTP and the web
     * console, and the panel keeps a copy so it can show it — the new value
     * is stored only after the container has accepted it, so a failed call
     * leaves the previous password valid.
     *
     * At least 12 characters; a colon or a line break is rejected because the
     * guest's account file uses them as separators.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function setRootPassword(string $uuid, string $password): array
    {
        return $this->client->patch("{$this->basePath}/{$uuid}/root-password", [
            'password' => $password,
        ]);
    }

    /**
     * Point a domain at this service, or pass null to clear it.
     *
     * A node shares one public IPv4 between all its containers, so a web
     * service is only reachable by name once the edge proxy knows which
     * hostname belongs to which container. This call saves the domain and
     * republishes the proxy in one step; HTTPS is issued automatically once
     * the name resolves to the node.
     *
     * Until a domain is set the service is still reachable on its forwarded
     * port — see allocations().
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function setDomain(string $uuid, ?string $domain): array
    {
        return $this->client->patch("{$this->basePath}/{$uuid}/domain", [
            'domain' => $domain,
        ]);
    }

    /**
     * Get live resource usage / status for a cloud service.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function status(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/status");
    }

    /**
     * The recorded resource history — CPU, memory, disk, network and disk I/O.
     *
     * `status()` is a single instantaneous reading, so charting it means
     * charting only what you observed while your own process was running.
     * These samples are collected server-side and outlive the caller, which is
     * what makes a "last 24 hours" view possible at all.
     *
     * Returned points are bucketed to keep the series drawable — a month at
     * raw resolution is tens of thousands of rows for a few hundred pixels.
     * `bucket` is that slot width in seconds and `ts` is milliseconds, ready
     * for a JavaScript Date.
     *
     * An unrecognised $range falls back to the default rather than failing;
     * the response echoes the range that was actually applied, alongside
     * `available_ranges`.
     *
     * @param  string  $range  One of 1h, 24h, 7d, 30d.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function metrics(string $uuid, string $range = '24h'): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/metrics", [
            'range' => $range,
        ]);
    }

    /**
     * Send a command to the service console.
     *
     * Only backends that run a single foreground process with a stdin to pipe
     * into support this. The LXC backend does not — a container has no such
     * process — and answers 501. Use the console session instead, or SSH.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function sendCommand(string $uuid, string $command): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/command", [
            'command' => $command,
        ]);
    }

    /**
     * Open a console session for the service.
     *
     * Cloud Services run as Proxmox LXC containers, whose console is an
     * interactive TTY on the node rather than a log stream, so this returns a
     * ready single-use URL instead of a token you assemble a socket from:
     *
     *   {
     *     "kind":           "url",
     *     "url":            "https://<console-proxy>/?token=...&type=serial",
     *     "expires_in_sec": 10
     *   }
     *
     * Open the URL directly (a browser tab or an iframe). The proxy consumes
     * the ticket on first connect, so the URL is single-use — call this again
     * for a new session rather than reusing one.
     *
     * Before the LXC backend this returned `token` + `websocket_url` +
     * `subprotocols` for a daemon socket; those keys are gone.
     *
     * @param string $type 'serial' for the xterm TTY, 'vnc' for the graphical
     *                     console. Anything else falls back to 'serial'.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function consoleToken(string $uuid, string $type = 'serial'): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/console-token", ['type' => $type]);
    }

    /**
     * List every port allocation assigned to the server, zipped 1:1 by
     * index with the cloud-service's `port_schema` so each entry
     * already carries its role + protocol + description alongside the
     * raw host:port.
     *
     * Use this instead of `get($uuid)` when you need to render a
     * "Network" view — YaTQA / SourceTV / ServerQuery bots expect a
     * specific role's port (`query`, `gotv`, `txadmin`, ...) and the
     * single `allocation_port` on the service record only ever shows
     * the first allocation. For TeamSpeak 3 the response includes the
     * voice (UDP) and the query + filetransfer (TCP) ports, each with
     * the description from the template's port_schema and the vendor
     * default (e.g. TS3 query defaults to 10011).
     *
     * Response shape:
     *
     *   {
     *     "data": [
     *       {"role":"voice", "protocol":"udp",
     *        "description":"TeamSpeak 3 voice (clients connect here)",
     *        "ip":"94.249.215.109", "port":25500, "default":9987},
     *       {"role":"query", "protocol":"tcp", "description":"...",
     *        "ip":"94.249.215.109", "port":25501, "default":10011},
     *       ...
     *     ],
     *     "service": {"slug":"teamspeak-arm64", "name":"..."}
     *   }
     *
     * Allocations beyond the schema length are returned with
     * role="extra"; schema entries with no matching allocation are
     * omitted so callers know to scale resources up.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function allocations(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/allocations");
    }

    // ==================== SUB-RESOURCES ====================

    /**
     * Power Control
     */
    public function power(): Power
    {
        return $this->powerHandler ??= new Power($this->client);
    }

    /**
     * File Management
     */
    public function files(): Files
    {
        return $this->filesHandler ??= new Files($this->client);
    }

    /**
     * Backup Management
     */
    public function backups(): Backups
    {
        return $this->backupsHandler ??= new Backups($this->client);
    }

    /**
     * Network — per-service addressing.
     *
     * `allocations()` on this class is the useful call. The per-server IPv6
     * ordering flow only applies to backends that advertise it: an LXC
     * container is given one address out of the node's prefix when it is
     * created, so there is nothing to order and those endpoints answer 501.
     */
    public function network(): Network
    {
        return $this->networkHandler ??= new Network($this->client);
    }

}

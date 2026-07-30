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
     *                      name, memory_limit, disk_limit, cpu_limit. Optional:
     *                      team_id, description, environment.
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
     * @throws ApiException
     * @throws GuzzleException
     */
    public function consoleToken(string $uuid): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/console-token");
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

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
    private ?Registries $registriesHandler = null;

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
     *         "slug": "teamspeak6-server",
     *         "name": "TeamSpeak 6 Server",
     *         "category": "Application",
     *         "description": "...",
     *         "docker_image": "ghcr.io/exbil/teamspeak6-server:latest",
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
     *                      team_id, description, environment, docker_image.
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
     * @param array  $options    Optional overrides forwarded to the daemon:
     *                            - cloudservice (string): switch to a new
     *                              template at the same time.
     *                            - environment (array): override env vars
     *                              for the (possibly new) template.
     *                            - auto_start (bool): when true, the daemon
     *                              runs startServer() the moment the wipe
     *                              + image pull completes, so the customer
     *                              sees the entrypoint's seed phase in
     *                              the live console without having to
     *                              click Start a second time.
     *                              Recommended for customer-facing flows.
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
     * Issue a short-lived scoped token + ready-to-use wss:// URLs for the
     * live console + stats WebSocket streams.
     *
     * Returns:
     *   {
     *     "token":               "cst_...",
     *     "subprotocols":        ["cst", "cst_..."],
     *     "websocket_url":       "wss://<node>:443/api/v1/servers/<uuid>/console",
     *     "stats_websocket_url": "wss://<node>:443/api/v1/servers/<uuid>/stats",
     *     "expires_in_sec":      300
     *   }
     *
     * Pass the array under the `subprotocols` key as the WebSocket
     * client's `Sec-WebSocket-Protocol` header — that's how the daemon
     * authenticates the connection. URLs go in proxy logs and browser
     * history, the subprotocol header doesn't, so the token never lands
     * anywhere it shouldn't. Token TTL is ~5 min; re-call this endpoint
     * to keep a long-running session alive.
     *
     * Browser example (vanilla JS):
     *   const ws = new WebSocket(data.websocket_url, data.subprotocols);
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
     * Network — per-server IPv6 lifecycle (status + order/list/release).
     * Up to four addresses per server out of the operator's routed /64
     * (or wider) prefix.
     */
    public function network(): Network
    {
        return $this->networkHandler ??= new Network($this->client);
    }

    /**
     * Container Registries — order, list, show, delete the private
     * registries customers can host their own Docker images in. Live
     * at /products/cloudservices/registries/* with their own pricing
     * + namespace-check helpers.
     */
    public function registries(): Registries
    {
        return $this->registriesHandler ??= new Registries($this->client);
    }
}

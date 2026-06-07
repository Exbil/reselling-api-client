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
}

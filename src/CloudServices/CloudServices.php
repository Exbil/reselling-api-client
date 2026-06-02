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
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reinstall(string $uuid): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/reinstall");
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
}

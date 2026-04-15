<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Databases
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all databases for a server
     *
     * @param int $serverId Server ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $serverId): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/databases");
    }

    /**
     * Create a new database
     *
     * @param int $serverId Server ID
     * @param array $data Database configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(int $serverId, array $data): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/databases", $data);
    }

    /**
     * Rotate database password
     *
     * @param int $serverId Server ID
     * @param int $dbId Database ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function rotatePassword(int $serverId, int $dbId): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/databases/{$dbId}/rotate-password");
    }

    /**
     * Delete a database
     *
     * @param int $serverId Server ID
     * @param int $dbId Database ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $serverId, int $dbId): array
    {
        return $this->client->delete("{$this->basePath}/{$serverId}/databases/{$dbId}");
    }
}

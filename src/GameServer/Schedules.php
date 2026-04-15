<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Schedules
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all schedules for a server
     *
     * @param int $serverId Server ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $serverId): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/schedules");
    }

    /**
     * Get a specific schedule
     *
     * @param int $serverId Server ID
     * @param int $scheduleId Schedule ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $serverId, int $scheduleId): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/schedules/{$scheduleId}");
    }

    /**
     * Create a new schedule
     *
     * @param int $serverId Server ID
     * @param array $data Schedule configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(int $serverId, array $data): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/schedules", $data);
    }

    /**
     * Update a schedule
     *
     * @param int $serverId Server ID
     * @param int $scheduleId Schedule ID
     * @param array $data Updated schedule data
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(int $serverId, int $scheduleId, array $data): array
    {
        return $this->client->put("{$this->basePath}/{$serverId}/schedules/{$scheduleId}", $data);
    }

    /**
     * Delete a schedule
     *
     * @param int $serverId Server ID
     * @param int $scheduleId Schedule ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $serverId, int $scheduleId): array
    {
        return $this->client->delete("{$this->basePath}/{$serverId}/schedules/{$scheduleId}");
    }

    /**
     * Execute a schedule immediately
     *
     * @param int $serverId Server ID
     * @param int $scheduleId Schedule ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function execute(int $serverId, int $scheduleId): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/schedules/{$scheduleId}/execute");
    }

    /**
     * Create a task within a schedule
     *
     * @param int $serverId Server ID
     * @param int $scheduleId Schedule ID
     * @param array $data Task configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createTask(int $serverId, int $scheduleId, array $data): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/schedules/{$scheduleId}/tasks", $data);
    }

    /**
     * Delete a task from a schedule
     *
     * @param int $serverId Server ID
     * @param int $scheduleId Schedule ID
     * @param int $taskId Task ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteTask(int $serverId, int $scheduleId, int $taskId): array
    {
        return $this->client->delete("{$this->basePath}/{$serverId}/schedules/{$scheduleId}/tasks/{$taskId}");
    }
}

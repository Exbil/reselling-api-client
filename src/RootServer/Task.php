<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Task
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all tasks
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(array $query = []): array
    {
        return $this->client->get("{$this->basePath}/tasks", $query);
    }

    /**
     * Get a specific task
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int|string $task): array
    {
        return $this->client->get("{$this->basePath}/tasks/{$task}");
    }

    /**
     * Cancel a task
     *
     * @param int|string $task Task ID
     * @param array $data Optional body
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function cancel(int|string $task, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/tasks/{$task}/cancel", $data);
    }

    /**
     * Get all tasks for a specific server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAllForServer(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/tasks", $query);
    }

    /**
     * Get a specific task for a server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getForServer(int $server, int|string $task): array
    {
        return $this->client->get("{$this->basePath}/{$server}/tasks/{$task}");
    }
}

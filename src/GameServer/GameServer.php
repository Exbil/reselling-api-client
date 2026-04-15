<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class GameServer
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';
    private ?Power $powerHandler = null;
    private ?Files $filesHandler = null;
    private ?Backups $backupsHandler = null;
    private ?Console $consoleHandler = null;
    private ?Schedules $schedulesHandler = null;
    private ?Databases $databasesHandler = null;
    private ?Eggs $eggsHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== SERVER LISTING ====================

    /**
     * Get all game servers with optional filters
     *
     * @param array $filters Available filters
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(array $filters = []): array
    {
        return $this->client->get($this->basePath, $filters);
    }

    /**
     * Get a specific game server by ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $id): array
    {
        return $this->client->get("{$this->basePath}/{$id}");
    }

    // ==================== SERVER CREATION & MODIFICATION ====================

    /**
     * Create a new game server
     *
     * @param array $config Server configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $config): array
    {
        return $this->client->post("{$this->basePath}", $config);
    }

    /**
     * Update a game server
     *
     * @param int $id Server ID
     * @param array $data Update data
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(int $id, array $data): array
    {
        return $this->client->put("{$this->basePath}/{$id}", $data);
    }

    /**
     * Delete a game server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $id): array
    {
        return $this->client->delete("{$this->basePath}/{$id}");
    }

    /**
     * Reinstall a game server
     *
     * @param int $id Server ID
     * @param array $config Reinstall options
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reinstall(int $id, array $config = []): array
    {
        return $this->client->post("{$this->basePath}/{$id}/reinstall", $config);
    }

    /**
     * Suspend a game server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function suspend(int $id): array
    {
        return $this->client->post("{$this->basePath}/{$id}/suspend");
    }

    /**
     * Unsuspend a game server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function unsuspend(int $id): array
    {
        return $this->client->post("{$this->basePath}/{$id}/unsuspend");
    }

    /**
     * Get game server status
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function status(int $id): array
    {
        return $this->client->get("{$this->basePath}/{$id}/status");
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
     * Console Access
     */
    public function console(): Console
    {
        return $this->consoleHandler ??= new Console($this->client);
    }

    /**
     * Schedule Management
     */
    public function schedules(): Schedules
    {
        return $this->schedulesHandler ??= new Schedules($this->client);
    }

    /**
     * Database Management
     */
    public function databases(): Databases
    {
        return $this->databasesHandler ??= new Databases($this->client);
    }

    /**
     * Available Games/Eggs
     */
    public function eggs(): Eggs
    {
        return $this->eggsHandler ??= new Eggs($this->client);
    }
}

<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Backups
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all backups for a server
     *
     * @param int $serverId Server ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $serverId): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/backups");
    }

    /**
     * Create a new backup
     *
     * @param int $serverId Server ID
     * @param string|null $name Optional backup name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(int $serverId, ?string $name = null): array
    {
        $data = [];
        if ($name !== null) {
            $data['name'] = $name;
        }
        return $this->client->post("{$this->basePath}/{$serverId}/backups", $data);
    }

    /**
     * Delete a backup
     *
     * @param int $serverId Server ID
     * @param int $backupId Backup ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $serverId, int $backupId): array
    {
        return $this->client->delete("{$this->basePath}/{$serverId}/backups/{$backupId}");
    }

    /**
     * Restore a backup
     *
     * @param int $serverId Server ID
     * @param int $backupId Backup ID
     * @param bool $truncate Whether to truncate existing files before restoring
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restore(int $serverId, int $backupId, bool $truncate = false): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/backups/{$backupId}/restore", [
            'truncate' => $truncate,
        ]);
    }

    /**
     * Download a backup
     *
     * @param int $serverId Server ID
     * @param int $backupId Backup ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function download(int $serverId, int $backupId): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/backups/{$backupId}/download");
    }

    /**
     * Toggle backup lock status
     *
     * @param int $serverId Server ID
     * @param int $backupId Backup ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function toggleLock(int $serverId, int $backupId): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/backups/{$backupId}/lock");
    }
}

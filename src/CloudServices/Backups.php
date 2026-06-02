<?php

namespace Exbil\ResellingAPI\CloudServices;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Backups
{
    private Client $client;
    private string $basePath = 'v1/products/cloudservices';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all backups for a service
     *
     * @param string $uuid Service UUID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/backups");
    }

    /**
     * Create a new backup
     *
     * @param string $uuid Service UUID
     * @param string|null $name Optional backup name
     * @param array $ignoredFiles Optional list of glob patterns to exclude
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $uuid, ?string $name = null, array $ignoredFiles = []): array
    {
        $data = [];
        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($ignoredFiles !== []) {
            $data['ignored_files'] = $ignoredFiles;
        }
        return $this->client->post("{$this->basePath}/{$uuid}/backups", $data);
    }

    /**
     * Delete a backup
     *
     * @param string $uuid Service UUID
     * @param string $backupId Backup ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $uuid, string $backupId): array
    {
        return $this->client->delete("{$this->basePath}/{$uuid}/backups/{$backupId}");
    }

    /**
     * Restore a backup
     *
     * @param string $uuid Service UUID
     * @param string $backupId Backup ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restore(string $uuid, string $backupId): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/backups/{$backupId}/restore");
    }

    /**
     * Download a backup (returns a download URL)
     *
     * @param string $uuid Service UUID
     * @param string $backupId Backup ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function download(string $uuid, string $backupId): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/backups/{$backupId}/download");
    }
}

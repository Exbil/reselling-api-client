<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Backup
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all backups for a server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/backups", $query);
    }

    /**
     * Get a specific backup
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $server, int|string $backup): array
    {
        return $this->client->get("{$this->basePath}/{$server}/backups/{$backup}");
    }

    /**
     * Create a backup
     *
     * Documented body fields: mode, compress, notes.
     *
     * @param int $server Server ID
     * @param string|null $mode
     * @param string|null $compress
     * @param string|null $notes
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(
        int $server,
        ?string $mode = null,
        ?string $compress = null,
        ?string $notes = null,
        array $extra = []
    ): array {
        $data = $extra;
        if ($mode !== null) {
            $data['mode'] = $mode;
        }
        if ($compress !== null) {
            $data['compress'] = $compress;
        }
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        return $this->client->post("{$this->basePath}/{$server}/backups/create", $data);
    }

    /**
     * Delete a backup
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $server, int|string $backup): array
    {
        return $this->client->post("{$this->basePath}/{$server}/backups/{$backup}/delete");
    }

    /**
     * Restore a backup
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restore(int $server, int|string $backup): array
    {
        return $this->client->post("{$this->basePath}/{$server}/backups/{$backup}/restore");
    }
}

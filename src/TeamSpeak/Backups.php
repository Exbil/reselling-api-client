<?php

namespace Exbil\ResellingAPI\TeamSpeak;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Backups
{
    private Client $client;
    private string $basePath = 'v1/products/teamspeak';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Download a snapshot backup of the virtual server.
     *
     * Returns the base64 snapshot under data.snapshot.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function download(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/backup");
    }

    /**
     * Restore the virtual server from a base64 snapshot. This overwrites the
     * server's channels, groups and permissions.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restore(int $id, string $snapshot): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/backup", [
            'snapshot' => $snapshot,
        ]);
    }
}

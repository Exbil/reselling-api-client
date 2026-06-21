<?php

namespace Exbil\ResellingAPI\TeamSpeak;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Clients
{
    private Client $client;
    private string $basePath = 'v1/products/teamspeak';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get details of a connected client (IP, version, country, connection time).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function details(int $id, int $clid): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/clients/{$clid}");
    }

    /**
     * Move a client to another channel.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function move(int $id, int $clid, int $cid): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/clients/move", [
            'clid' => $clid,
            'cid' => $cid,
        ]);
    }

    /**
     * Kick a client from the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function kick(int $id, int $clid, string $reason = ''): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/kick", [
            'clid' => $clid,
            'reason' => $reason,
        ]);
    }

    /**
     * Ban a connected client.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function ban(int $id, int $clid, int $seconds = 3600, string $reason = ''): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/ban", [
            'clid' => $clid,
            'seconds' => $seconds,
            'reason' => $reason,
        ]);
    }
}

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
     * List all connected clients of the virtual server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/clients");
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
     * Kick a client from the channel or the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function kick(int $id, int $clid, string $reason = '', string $type = '', array $extra = []): array
    {
        $data = ['clid' => $clid, 'reason' => $reason];
        if ($type !== '') {
            $data['type'] = $type;
        }

        return $this->client->post("{$this->basePath}/servers/{$id}/clients/kick", array_merge($data, $extra));
    }

    /**
     * Send a private text message to a connected client.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function message(int $id, int $clid, string $message): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/clients/message", [
            'clid' => $clid,
            'message' => $message,
        ]);
    }

    /**
     * Poke a connected client with a message.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function poke(int $id, int $clid, string $message): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/clients/poke", [
            'clid' => $clid,
            'message' => $message,
        ]);
    }
}

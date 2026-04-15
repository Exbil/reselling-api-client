<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Console
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send a command to the server console
     *
     * @param int $serverId Server ID
     * @param string $command Command to execute
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function sendCommand(int $serverId, string $command): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/console/command", [
            'command' => $command,
        ]);
    }

    /**
     * Get WebSocket URL for live console access
     *
     * @param int $serverId Server ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getWebSocketUrl(int $serverId): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/console/websocket");
    }
}

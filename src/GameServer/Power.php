<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Power
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Start server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function start(int $id): array
    {
        return $this->client->post("{$this->basePath}/{$id}/power/start");
    }

    /**
     * Stop server (graceful shutdown)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stop(int $id): array
    {
        return $this->client->post("{$this->basePath}/{$id}/power/stop");
    }

    /**
     * Restart server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restart(int $id): array
    {
        return $this->client->post("{$this->basePath}/{$id}/power/restart");
    }

    /**
     * Kill server (force stop)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function kill(int $id): array
    {
        return $this->client->post("{$this->basePath}/{$id}/power/kill");
    }
}

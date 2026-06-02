<?php

namespace Exbil\ResellingAPI\CloudServices;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Power
{
    private Client $client;
    private string $basePath = 'v1/products/cloudservices';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Start service
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function start(string $uuid): array
    {
        return $this->send($uuid, 'start');
    }

    /**
     * Stop service (graceful shutdown)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stop(string $uuid): array
    {
        return $this->send($uuid, 'stop');
    }

    /**
     * Restart service
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restart(string $uuid): array
    {
        return $this->send($uuid, 'restart');
    }

    /**
     * Kill service (force stop)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function kill(string $uuid): array
    {
        return $this->send($uuid, 'kill');
    }

    /**
     * Send a power action.
     *
     * @param string $action One of: start, stop, restart, kill
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function send(string $uuid, string $action): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/power", [
            'action' => $action,
        ]);
    }
}

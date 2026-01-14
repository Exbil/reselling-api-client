<?php

namespace Exbil\CloudApi\RootServer;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Power
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

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
    public function start(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/power/start");
    }

    /**
     * Stop server (graceful shutdown)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stop(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/power/stop");
    }

    /**
     * Reboot server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reboot(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/power/reboot");
    }

    /**
     * Force stop server (power off)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function forceStop(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/power/force-stop");
    }
}

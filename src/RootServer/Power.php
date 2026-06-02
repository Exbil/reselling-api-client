<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
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
        return $this->client->post("{$this->basePath}/{$vmId}/start");
    }

    /**
     * Stop server (graceful shutdown)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stop(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/stop");
    }

    /**
     * Restart server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reboot(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/restart");
    }

    /**
     * Kill server (force stop / power off)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function forceStop(int $vmId): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/kill");
    }
}

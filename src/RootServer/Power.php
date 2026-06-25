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
     * @param int $server Server ID
     * @param array $data Optional body (e.g. ['context' => ['boot' => true]])
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function start(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/start", $data);
    }

    /**
     * Stop server (graceful shutdown)
     *
     * @param int $server Server ID
     * @param array $data Optional body (e.g. ['context' => ['boot' => true]])
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stop(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/stop", $data);
    }

    /**
     * Restart server
     *
     * @param int $server Server ID
     * @param array $data Optional body (e.g. ['context' => ['boot' => true]])
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reboot(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/restart", $data);
    }

    /**
     * Kill server (force stop / power off)
     *
     * @param int $server Server ID
     * @param array $data Optional body (e.g. ['context' => ['boot' => true]])
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function forceStop(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/kill", $data);
    }
}

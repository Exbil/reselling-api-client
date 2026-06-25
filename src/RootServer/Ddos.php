<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Ddos
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the DDoS protection information for a server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/ddos", $query);
    }
}

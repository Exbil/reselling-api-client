<?php

namespace Exbil\CloudApi\RootServer;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Location
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all available datacenters/locations
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get("{$this->basePath}/locations");
    }

    /**
     * Get clusters for a specific datacenter
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getClusters(string $datacenterSlug): array
    {
        return $this->client->get("{$this->basePath}/locations/{$datacenterSlug}/clusters");
    }
}

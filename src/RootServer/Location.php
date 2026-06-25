<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
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
     * Get a single location (by numeric id or slug), including its clusters
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int|string $location): array
    {
        return $this->client->get("{$this->basePath}/locations/{$location}");
    }

    /**
     * Get clusters for a specific datacenter
     *
     * @param int|string $datacenter Datacenter id or slug
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getClusters(int|string $datacenter): array
    {
        return $this->client->get("{$this->basePath}/locations/{$datacenter}/clusters");
    }
}

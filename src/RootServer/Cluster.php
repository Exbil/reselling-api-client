<?php

namespace Exbil\CloudApi\RootServer;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Cluster
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all clusters
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get("{$this->basePath}/clusters");
    }

    /**
     * Get a specific cluster
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $clusterSlug): array
    {
        return $this->client->get("{$this->basePath}/clusters/{$clusterSlug}");
    }

    /**
     * Get available OS versions for a cluster
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getOsList(string $clusterSlug): array
    {
        return $this->client->get("{$this->basePath}/clusters/{$clusterSlug}/os-list");
    }

    /**
     * Get price list for a cluster
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPrices(string $clusterSlug): array
    {
        return $this->client->get("{$this->basePath}/clusters/{$clusterSlug}/prices");
    }

    /**
     * Calculate price for a server configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function calculatePrice(string $clusterSlug, array $config): array
    {
        return $this->client->post("{$this->basePath}/clusters/{$clusterSlug}/price-calc", $config);
    }
}

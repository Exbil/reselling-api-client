<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
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
     * @param string $cluster Cluster slug or ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $cluster): array
    {
        return $this->client->get("{$this->basePath}/clusters/{$cluster}");
    }

    /**
     * Get available OS versions for a cluster
     *
     * @param string $cluster Cluster slug or ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getOsList(string $cluster): array
    {
        return $this->client->get("{$this->basePath}/cluster/{$cluster}/os-list");
    }

    /**
     * Get the price list for a cluster
     *
     * @param string $cluster Cluster slug or ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPricing(string $cluster): array
    {
        return $this->client->get("{$this->basePath}/cluster/{$cluster}/pricing");
    }

    /**
     * Calculate the price for a server configuration
     *
     * Documented body fields: cores, ram_mb, disk_gb (required), plus optional
     * datacenter_id, datacenter_slug, backup_slots, ipv4_addresses,
     * ipv6_addresses, ipv4_count, ipv6_count, ipv4, ipv6.
     *
     * @param string $cluster Cluster slug or ID
     * @param int $cores
     * @param int $ramMb
     * @param int $diskGb
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function calculatePrice(string $cluster, int $cores, int $ramMb, int $diskGb, array $extra = []): array
    {
        $data = array_merge($extra, [
            'cores' => $cores,
            'ram_mb' => $ramMb,
            'disk_gb' => $diskGb,
        ]);
        return $this->client->post("{$this->basePath}/cluster/{$cluster}/calculator", $data);
    }
}

<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Vpc
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all VPCs
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(array $query = []): array
    {
        return $this->client->get("{$this->basePath}/vpc", $query);
    }

    /**
     * Get a specific VPC
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int|string $vpc): array
    {
        return $this->client->get("{$this->basePath}/vpc/{$vpc}");
    }

    /**
     * Create a VPC
     *
     * Documented body fields: name, cluster_id, subnet_cidr (required), plus
     * optional gateway_ip, vlan_tag, ixp_enabled, ixp_asn.
     *
     * @param string $name
     * @param int $clusterId
     * @param string $subnetCidr
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $name, int $clusterId, string $subnetCidr, array $extra = []): array
    {
        $data = array_merge($extra, [
            'name' => $name,
            'cluster_id' => $clusterId,
            'subnet_cidr' => $subnetCidr,
        ]);
        return $this->client->post("{$this->basePath}/vpc/create", $data);
    }

    /**
     * Delete a VPC
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int|string $vpc): array
    {
        return $this->client->post("{$this->basePath}/vpc/{$vpc}/delete");
    }

    /**
     * Link a server to a VPC
     *
     * @param int|string $vpc VPC ID
     * @param int $serverId Server ID
     * @param string|null $internalIp Internal IP to assign
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function link(int|string $vpc, int $serverId, ?string $internalIp = null, array $extra = []): array
    {
        $data = $extra;
        $data['server_id'] = $serverId;
        if ($internalIp !== null) {
            $data['internal_ip'] = $internalIp;
        }
        return $this->client->post("{$this->basePath}/vpc/{$vpc}/link", $data);
    }

    /**
     * Unlink a server from a VPC
     *
     * @param int|string $vpc VPC ID
     * @param int $serverId Server ID
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function unlink(int|string $vpc, int $serverId, array $extra = []): array
    {
        $data = array_merge($extra, ['server_id' => $serverId]);
        return $this->client->post("{$this->basePath}/vpc/{$vpc}/unlink", $data);
    }
}

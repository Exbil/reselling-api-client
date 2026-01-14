<?php

namespace Exbil\CloudApi\VPN;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Config
{
    private Client $client;
    private string $basePath = 'v1/products/vpn';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get OpenVPN configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getOpenVpn(int $accountId, int $serverId, int $portId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/openvpn", [
            'server_id' => $serverId,
            'port_id' => $portId,
        ]);
    }

    /**
     * Download OpenVPN configuration file (.ovpn)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function downloadOpenVpn(int $accountId, int $serverId, int $portId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/openvpn/download", [
            'server_id' => $serverId,
            'port_id' => $portId,
        ]);
    }

    /**
     * Get WireGuard configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getWireGuard(int $accountId, int $serverId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/wireguard", [
            'server_id' => $serverId,
        ]);
    }

    /**
     * Download WireGuard configuration file (.conf)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function downloadWireGuard(int $accountId, int $serverId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/wireguard/download", [
            'server_id' => $serverId,
        ]);
    }
}

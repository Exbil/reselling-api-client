<?php

namespace Exbil\CloudApi\VPN;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class VPN
{
    private Client $client;
    private string $basePath = 'v1/products/vpn';
    private ?Account $accountHandler = null;
    private ?Config $configHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== PUBLIC INFORMATION ====================

    /**
     * Get all available VPN servers
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getServers(): array
    {
        return $this->client->get("{$this->basePath}/servers");
    }

    /**
     * Get all available VPN ports
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPorts(): array
    {
        return $this->client->get("{$this->basePath}/ports");
    }

    /**
     * Get VPN pricing
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPricing(): array
    {
        return $this->client->get("{$this->basePath}/pricing");
    }

    /**
     * Get GeoIP info for current request
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getGeoIP(): array
    {
        return $this->client->get("{$this->basePath}/geoip");
    }

    /**
     * Check if a username is available
     *
     * @param string $username Username to check (3-50 chars)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function checkUsername(string $username): array
    {
        return $this->client->post("{$this->basePath}/check-username", [
            'username' => $username,
        ]);
    }

    /**
     * Account Management
     */
    public function account(): Account
    {
        return $this->accountHandler ??= new Account($this->client);
    }

    /**
     * Configuration Management
     */
    public function config(): Config
    {
        return $this->configHandler ??= new Config($this->client);
    }
}

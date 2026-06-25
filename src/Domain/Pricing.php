<?php

namespace Exbil\ResellingAPI\Domain;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Pricing
{
    private Client $client;
    private string $basePath = 'v1/products/domains';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all domain prices
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get("{$this->basePath}/prices");
    }

    /**
     * Get available TLDs
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getTlds(): array
    {
        return $this->client->get("{$this->basePath}/prices/tlds");
    }

    /**
     * Get pricing for a specific TLD
     *
     * @param string $tld TLD (e.g., "com", "de", "net")
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getByTld(string $tld): array
    {
        return $this->client->get("{$this->basePath}/prices/{$tld}");
    }
}

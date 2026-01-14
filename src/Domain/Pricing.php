<?php

namespace Exbil\CloudApi\Domain;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Pricing
{
    private Client $client;

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
        return $this->client->get('v1/domains/prices');
    }

    /**
     * Get available TLDs
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getTlds(): array
    {
        return $this->client->get('v1/domains/prices/tlds');
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
        return $this->client->get("v1/domains/prices/{$tld}");
    }
}

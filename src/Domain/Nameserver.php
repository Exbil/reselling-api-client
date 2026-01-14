<?php

namespace Exbil\CloudApi\Domain;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Nameserver
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get domain nameservers
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $domain): array
    {
        return $this->client->get("v1/domains/{$domain}/nameservers");
    }

    /**
     * Update domain nameservers
     *
     * @param string $domain Domain name
     * @param array $nameservers List of nameservers
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $domain, array $nameservers): array
    {
        return $this->client->put("v1/domains/{$domain}/nameservers", [
            'nameservers' => $nameservers,
        ]);
    }
}

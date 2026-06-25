<?php

namespace Exbil\ResellingAPI\Domain;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Nameserver
{
    private Client $client;
    private string $basePath = 'v1/products/domains';

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
        return $this->client->get("{$this->basePath}/{$domain}/nameservers");
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
        return $this->client->put("{$this->basePath}/{$domain}/nameservers", [
            'nameservers' => array_values($nameservers),
        ]);
    }
}

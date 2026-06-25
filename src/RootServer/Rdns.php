<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Rdns
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the reverse DNS entries for a server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/rdns", $query);
    }

    /**
     * Update the reverse DNS (PTR) record for an IP
     *
     * @param int $server Server ID
     * @param string $ip IP address
     * @param string|null $ptr PTR record value
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(int $server, string $ip, ?string $ptr = null, array $extra = []): array
    {
        $data = $extra;
        $data['ip'] = $ip;
        if ($ptr !== null) {
            $data['ptr'] = $ptr;
        }
        return $this->client->post("{$this->basePath}/{$server}/rdns/update", $data);
    }
}

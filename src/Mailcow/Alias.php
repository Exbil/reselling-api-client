<?php

namespace Exbil\ResellingAPI\Mailcow;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Alias
{
    private Client $client;
    private string $basePath = 'v1/products/mailcow';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a specific alias
     *
     * @param string $domain Domain name
     * @param int $aliasId Alias ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $domain, int $aliasId): array
    {
        return $this->client->get("{$this->basePath}/{$domain}/aliases/{$aliasId}");
    }

    /**
     * List all aliases of a domain.
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string $domain): array
    {
        return $this->client->get("{$this->basePath}/{$domain}/aliases");
    }

    /**
     * Create an alias
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $goto Destination email addresses
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $domain, string $address, array $goto): array
    {
        return $this->client->post("{$this->basePath}/{$domain}/aliases", [
            'address' => $address,
            'goto' => array_values($goto),
        ]);
    }

    /**
     * Update an alias
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $goto Destination email addresses
     * @param bool|null $active Active status (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $domain, string $address, array $goto, ?bool $active = null): array
    {
        $data = [
            'address' => $address,
            'goto' => array_values($goto),
        ];
        if ($active !== null) {
            $data['active'] = $active;
        }
        return $this->client->put("{$this->basePath}/{$domain}/aliases", $data);
    }

    /**
     * Delete an alias
     *
     * @param string $domain Domain name
     * @param string $localPart Local part of the alias
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $domain, string $localPart): array
    {
        return $this->client->delete("{$this->basePath}/{$domain}/aliases/{$localPart}");
    }
}

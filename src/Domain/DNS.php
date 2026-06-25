<?php

namespace Exbil\ResellingAPI\Domain;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class DNS
{
    private Client $client;
    private string $basePath = 'v1/products/domains';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get DNS records for a domain
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $domain): array
    {
        return $this->client->get("{$this->basePath}/{$domain}/dns");
    }

    /**
     * Create a DNS record
     *
     * @param string $domain Domain name
     * @param string $type Record type (A, AAAA, CNAME, MX, TXT, etc.)
     * @param string $name Record name
     * @param string $content Record content
     * @param array $extra Optional fields: ttl, priority
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $domain, string $type, string $name, string $content, array $extra = []): array
    {
        return $this->client->post("{$this->basePath}/{$domain}/dns", array_merge([
            'type' => $type,
            'name' => $name,
            'content' => $content,
        ], $extra));
    }

    /**
     * Update a DNS record
     *
     * @param string $domain Domain name
     * @param string|int $recordId Record ID
     * @param array $data Record data to update (type, name, content, ttl, priority)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $domain, string|int $recordId, array $data): array
    {
        return $this->client->put("{$this->basePath}/{$domain}/dns/{$recordId}", $data);
    }

    /**
     * Bulk replace DNS records
     *
     * @param string $domain Domain name
     * @param array $records Array of record data
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function bulkUpdate(string $domain, array $records): array
    {
        return $this->client->put("{$this->basePath}/{$domain}/dns", [
            'records' => array_values($records),
        ]);
    }

    /**
     * Delete a DNS record
     *
     * @param string $domain Domain name
     * @param string|int $recordId Record ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $domain, string|int $recordId): array
    {
        return $this->client->delete("{$this->basePath}/{$domain}/dns/{$recordId}");
    }

    /**
     * Get DNS zones for a domain
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getZones(string $domain): array
    {
        return $this->client->get("{$this->basePath}/{$domain}/dns/zones");
    }

    /**
     * Create a DNS zone
     *
     * @param string $domain Domain name
     * @param string $name Zone name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createZone(string $domain, string $name): array
    {
        return $this->client->post("{$this->basePath}/{$domain}/dns/zones", [
            'name' => $name,
        ]);
    }

    /**
     * Update a DNS zone
     *
     * @param string $domain Domain name
     * @param string|int $zoneId Zone ID
     * @param string $name Zone name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateZone(string $domain, string|int $zoneId, string $name): array
    {
        return $this->client->put("{$this->basePath}/{$domain}/dns/zones/{$zoneId}", [
            'name' => $name,
        ]);
    }

    /**
     * Delete a DNS zone
     *
     * @param string $domain Domain name
     * @param string|int $zoneId Zone ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteZone(string $domain, string|int $zoneId): array
    {
        return $this->client->delete("{$this->basePath}/{$domain}/dns/zones/{$zoneId}");
    }
}

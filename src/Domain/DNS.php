<?php

namespace Exbil\CloudApi\Domain;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class DNS
{
    private Client $client;

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
        return $this->client->get("v1/domains/{$domain}/dns");
    }

    /**
     * Create a DNS record
     *
     * @param string $domain Domain name
     * @param array $record Record data:
     *   - type: string (A, AAAA, CNAME, MX, TXT, etc.)
     *   - name: string
     *   - content: string
     *   - ttl: int (optional)
     *   - priority: int (for MX records)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $domain, array $record): array
    {
        return $this->client->post("v1/domains/{$domain}/dns", $record);
    }

    /**
     * Update a DNS record
     *
     * @param string $domain Domain name
     * @param string|int $recordId Record ID
     * @param array $data Record data to update
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $domain, string|int $recordId, array $data): array
    {
        return $this->client->put("v1/domains/{$domain}/dns/{$recordId}", $data);
    }

    /**
     * Bulk update DNS records
     *
     * @param string $domain Domain name
     * @param array $records Array of record data
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function bulkUpdate(string $domain, array $records): array
    {
        return $this->client->put("v1/domains/{$domain}/dns", [
            'records' => $records,
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
        return $this->client->delete("v1/domains/{$domain}/dns/{$recordId}");
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
        return $this->client->get("v1/domains/{$domain}/dns/zones");
    }

    /**
     * Create a DNS zone
     *
     * @param string $domain Domain name
     * @param array $zone Zone data
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createZone(string $domain, array $zone): array
    {
        return $this->client->post("v1/domains/{$domain}/dns/zones", $zone);
    }

    /**
     * Update a DNS zone
     *
     * @param string $domain Domain name
     * @param string|int $zoneId Zone ID
     * @param array $data Zone data to update
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateZone(string $domain, string|int $zoneId, array $data): array
    {
        return $this->client->put("v1/domains/{$domain}/dns/zones/{$zoneId}", $data);
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
        return $this->client->delete("v1/domains/{$domain}/dns/zones/{$zoneId}");
    }
}

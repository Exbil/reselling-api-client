<?php

namespace Exbil\ResellingAPI\Mailcow;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Mailbox
{
    private Client $client;
    private string $basePath = 'v1/products/mailcow';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a specific mailbox
     *
     * @param string $domain Domain name
     * @param int $mailboxId Mailbox ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $domain, int $mailboxId): array
    {
        return $this->client->get("{$this->basePath}/{$domain}/mailboxes/{$mailboxId}");
    }

    /**
     * List all mailboxes of a domain.
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string $domain): array
    {
        return $this->client->get("{$this->basePath}/{$domain}/mailboxes");
    }

    /**
     * Create a mailbox
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $config Mailbox configuration:
     *   - name: string (optional)
     *   - password: string (optional)
     *   - quota_mb: int (optional)
     *   - active: bool (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $domain, string $address, array $config = []): array
    {
        $config['address'] = $address;
        return $this->client->post("{$this->basePath}/{$domain}/mailboxes", $config);
    }

    /**
     * Update a mailbox
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $config Update options:
     *   - name: string (optional)
     *   - password: string (optional)
     *   - quota_mb: int (optional)
     *   - active: bool (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $domain, string $address, array $config = []): array
    {
        $config['address'] = $address;
        return $this->client->put("{$this->basePath}/{$domain}/mailboxes", $config);
    }

    /**
     * Delete a mailbox
     *
     * @param string $domain Domain name
     * @param string $localPart Local part of the mailbox
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $domain, string $localPart): array
    {
        return $this->client->delete("{$this->basePath}/{$domain}/mailboxes/{$localPart}");
    }
}

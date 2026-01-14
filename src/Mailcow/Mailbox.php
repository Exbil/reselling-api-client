<?php

namespace Exbil\CloudApi\Mailcow;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
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
     * Get mailboxes for a domain
     *
     * @param string $domain Domain name
     * @param int|null $mailboxId Optional mailbox ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string $domain, ?int $mailboxId = null): array
    {
        $path = "{$this->basePath}/{$domain}/mailboxes";
        if ($mailboxId !== null) {
            $path .= "/{$mailboxId}";
        }
        return $this->client->get($path);
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
     * Create a mailbox
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $config Mailbox configuration:
     *   - password: string (optional)
     *   - name: string (optional)
     *   - quota_mb: int (optional)
     *   - active: boolean (optional)
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
     *   - password: string (min 12 chars, optional)
     *   - name: string (optional)
     *   - quota_mb: int (optional)
     *   - active: boolean (optional)
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

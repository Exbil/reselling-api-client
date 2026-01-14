<?php

namespace Exbil\CloudApi\Mailcow;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Mailcow
{
    private Client $client;
    private string $basePath = 'v1/products/mailcow';
    private ?Mailbox $mailboxHandler = null;
    private ?Alias $aliasHandler = null;
    private ?DomainAdmin $domainAdminHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== INFRASTRUCTURE ====================

    /**
     * Get all active Mailcow nodes
     *
     * @param string|null $datacenter Optional datacenter slug filter
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getNodes(?string $datacenter = null): array
    {
        $query = [];
        if ($datacenter !== null) {
            $query['datacenter'] = $datacenter;
        }
        return $this->client->get("{$this->basePath}/nodes", $query);
    }

    /**
     * Get load balancer statistics for nodes
     *
     * @param string|null $datacenter Optional datacenter slug filter
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getLoadBalancerStats(?string $datacenter = null): array
    {
        $query = [];
        if ($datacenter !== null) {
            $query['datacenter'] = $datacenter;
        }
        return $this->client->get("{$this->basePath}/load-balancer/stats", $query);
    }

    /**
     * Calculate pricing for mailbox resources
     *
     * @param string $nodeOrDatacenter Node ID/slug or datacenter ID/slug
     * @param int $mailboxes Number of mailboxes
     * @param int $aliases Number of aliases
     * @param int $quotaMb Quota in MB
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function calculatePrice(string $nodeOrDatacenter, int $mailboxes, int $aliases, int $quotaMb): array
    {
        return $this->client->post("{$this->basePath}/{$nodeOrDatacenter}/calculate", [
            'mailboxes' => $mailboxes,
            'aliases' => $aliases,
            'quota_mb' => $quotaMb,
        ]);
    }

    // ==================== DOMAIN MANAGEMENT ====================

    /**
     * Get all domains or a specific domain
     *
     * @param string|int|null $id Domain name or ID (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string|int|null $id = null): array
    {
        $path = "{$this->basePath}/domains";
        if ($id !== null) {
            $path .= "/{$id}";
        }
        return $this->client->get($path);
    }

    /**
     * Get a specific domain
     *
     * @param string|int $id Domain name or ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string|int $id): array
    {
        return $this->client->get("{$this->basePath}/domains/{$id}");
    }

    /**
     * Create one or multiple Mailcow domains
     *
     * @param string $nodeOrDatacenter Node ID/slug or datacenter ID/slug
     * @param array $config Domain configuration:
     *   - domain: string (single domain) OR domains: array (multiple)
     *   - mailboxes: int
     *   - aliases: int
     *   - quota_mb: int
     *   - defquota_mb: int (default quota per mailbox)
     *   - maxquota_mb: int (max quota per mailbox)
     *   - backupmx: int (0 or 1)
     *   - admin_username: string
     *   - admin_password: string
     *   - permissions: array
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $nodeOrDatacenter, array $config): array
    {
        return $this->client->post("{$this->basePath}/{$nodeOrDatacenter}/create", $config);
    }

    /**
     * Update domain configuration
     *
     * @param string|int $id Domain name or ID
     * @param array $config Update options:
     *   - active: int (0 or 1)
     *   - aliases: int
     *   - mailboxes: int
     *   - defquota_mb: int
     *   - maxquota_mb: int
     *   - quota_mb: int
     *   - backupmx: int (0 or 1)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string|int $id, array $config): array
    {
        return $this->client->put("{$this->basePath}/domains/{$id}", $config);
    }

    /**
     * Delete a domain
     *
     * @param string|int $id Domain name or ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string|int $id): array
    {
        return $this->client->delete("{$this->basePath}/domains/{$id}");
    }

    /**
     * Mailbox Management
     */
    public function mailbox(): Mailbox
    {
        return $this->mailboxHandler ??= new Mailbox($this->client);
    }

    /**
     * Alias Management
     */
    public function alias(): Alias
    {
        return $this->aliasHandler ??= new Alias($this->client);
    }

    /**
     * Domain Admin Management
     */
    public function domainAdmin(): DomainAdmin
    {
        return $this->domainAdminHandler ??= new DomainAdmin($this->client);
    }
}

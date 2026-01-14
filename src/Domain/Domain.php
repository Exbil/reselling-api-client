<?php

namespace Exbil\CloudApi\Domain;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Domain
{
    private Client $client;
    private ?DNS $dnsHandler = null;
    private ?Nameserver $nameserverHandler = null;
    private ?Handle $handleHandler = null;
    private ?Pricing $pricingHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all domains
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get('v1/domains');
    }

    /**
     * Get a specific domain
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $domain): array
    {
        return $this->client->get("v1/domains/{$domain}");
    }

    /**
     * Check domain availability
     *
     * @param string $domain Domain name to check
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function checkAvailability(string $domain): array
    {
        return $this->client->post('v1/domains/check', [
            'domain' => $domain,
        ]);
    }

    /**
     * Register a new domain
     *
     * @param string $domain Domain name
     * @param array $handles Handle configuration:
     *   - owner_handle: string|int
     *   - admin_handle: string|int
     *   - tech_handle: string|int
     *   - billing_handle: string|int (optional)
     * @param array $nameservers List of nameservers (optional)
     * @param int $period Registration period in years (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function register(string $domain, array $handles, array $nameservers = [], int $period = 1): array
    {
        return $this->client->post('v1/domains/register', array_merge([
            'domain' => $domain,
            'period' => $period,
            'nameservers' => $nameservers,
        ], $handles));
    }

    /**
     * Transfer a domain
     *
     * @param string $domain Domain name
     * @param string $authcode Authorization code
     * @param array $handles Handle configuration
     * @param array $nameservers List of nameservers (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function transfer(string $domain, string $authcode, array $handles, array $nameservers = []): array
    {
        return $this->client->post('v1/domains/transfer', array_merge([
            'domain' => $domain,
            'authcode' => $authcode,
            'nameservers' => $nameservers,
        ], $handles));
    }

    /**
     * Sync domain data from registrar
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function sync(string $domain): array
    {
        return $this->client->post("v1/domains/{$domain}/sync");
    }

    /**
     * Get domain authcode
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAuthcode(string $domain): array
    {
        return $this->client->get("v1/domains/{$domain}/authcode");
    }

    /**
     * Update domain handles
     *
     * @param string $domain Domain name
     * @param array $handles Handle configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateHandles(string $domain, array $handles): array
    {
        return $this->client->put("v1/domains/{$domain}/handles", $handles);
    }

    /**
     * Request domain deletion
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function requestDeletion(string $domain): array
    {
        return $this->client->post("v1/domains/{$domain}/delete");
    }

    /**
     * Cancel domain deletion
     *
     * @param string $domain Domain name
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function cancelDeletion(string $domain): array
    {
        return $this->client->post("v1/domains/{$domain}/undelete");
    }

    /**
     * DNS Management
     */
    public function dns(): DNS
    {
        return $this->dnsHandler ??= new DNS($this->client);
    }

    /**
     * Nameserver Management
     */
    public function nameserver(): Nameserver
    {
        return $this->nameserverHandler ??= new Nameserver($this->client);
    }

    /**
     * Handle/Contact Management
     */
    public function handle(): Handle
    {
        return $this->handleHandler ??= new Handle($this->client);
    }

    /**
     * Pricing Information
     */
    public function pricing(): Pricing
    {
        return $this->pricingHandler ??= new Pricing($this->client);
    }
}

<?php

namespace Exbil\ResellingAPI\Domain;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Domain
{
    private Client $client;
    private string $basePath = 'v1/products/domains';
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
        return $this->client->get($this->basePath);
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
        return $this->client->get("{$this->basePath}/{$domain}");
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
        return $this->client->post("{$this->basePath}/check", [
            'domain' => $domain,
        ]);
    }

    /**
     * Bulk check domain availability
     *
     * Checks the availability of up to 50 domains in a single request.
     *
     * @param array $domains List of domain names to check (max 50)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function checkBulkAvailability(array $domains): array
    {
        return $this->client->post("{$this->basePath}/check-bulk", [
            'domains' => array_values($domains),
        ]);
    }

    /**
     * Register a new domain
     *
     * @param string $domain Domain name
     * @param string $handleId Contact handle ID used for all roles
     * @param array $nameservers List of nameservers (optional)
     * @param int $years Registration period in years (optional)
     * @param array $extra Additional fields: auto_renew, privacy_protection,
     *                     dnssec_enabled, notes
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function register(string $domain, string $handleId, array $nameservers = [], int $years = 1, array $extra = []): array
    {
        return $this->client->post("{$this->basePath}/register", array_merge([
            'domain' => $domain,
            'handle_id' => $handleId,
            'nameservers' => array_values($nameservers),
            'years' => $years,
        ], $extra));
    }

    /**
     * Transfer a domain
     *
     * @param string $domain Domain name
     * @param string $authcode Authorization code
     * @param string $handleId Contact handle ID used for all roles
     * @param array $nameservers List of nameservers (optional)
     * @param int $years Registration period in years (optional)
     * @param array $extra Additional fields: auto_renew, privacy_protection,
     *                     dnssec_enabled, notes
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function transfer(string $domain, string $authcode, string $handleId, array $nameservers = [], int $years = 1, array $extra = []): array
    {
        return $this->client->post("{$this->basePath}/transfer", array_merge([
            'domain' => $domain,
            'authcode' => $authcode,
            'handle_id' => $handleId,
            'nameservers' => array_values($nameservers),
            'years' => $years,
        ], $extra));
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
        return $this->client->post("{$this->basePath}/{$domain}/sync");
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
        return $this->client->get("{$this->basePath}/{$domain}/authcode");
    }

    /**
     * Update domain contact handles
     *
     * @param string $domain Domain name
     * @param array $handles Handle configuration:
     *   - owner_handle_id: string
     *   - admin_handle_id: string
     *   - tech_handle_id: string
     *   - billing_handle_id: string
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateHandles(string $domain, array $handles): array
    {
        return $this->client->put("{$this->basePath}/{$domain}/handles", $handles);
    }

    /**
     * Toggle automatic renewal for a domain
     *
     * @param string $domain Domain name
     * @param bool $autoRenew Whether auto-renew should be enabled
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function setAutoRenew(string $domain, bool $autoRenew): array
    {
        return $this->client->put("{$this->basePath}/{$domain}/auto-renew", [
            'auto_renew' => $autoRenew,
        ]);
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
        return $this->client->post("{$this->basePath}/{$domain}/delete");
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
        return $this->client->post("{$this->basePath}/{$domain}/undelete");
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

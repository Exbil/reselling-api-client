<?php

namespace Exbil\ResellingAPI\Mailcow;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class DomainAdmin
{
    private Client $client;
    private string $basePath = 'v1/products/mailcow';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a specific domain admin
     *
     * @param string $domain Domain name
     * @param int $adminId Admin ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $domain, int $adminId): array
    {
        return $this->client->get("{$this->basePath}/domain-admin/{$domain}/{$adminId}");
    }

    /**
     * Create a domain admin
     *
     * @param string $domain Domain name
     * @param string $username Admin username
     * @param string|null $password Password (auto-generated if null)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $domain, string $username, ?string $password = null): array
    {
        $data = ['username' => $username];
        if ($password !== null) {
            $data['password'] = $password;
        }
        return $this->client->post("{$this->basePath}/domain-admin/{$domain}", $data);
    }

    /**
     * Update a domain admin
     *
     * @param string $domain Domain name
     * @param string $username Current username
     * @param array $config Update options:
     *   - username_new: string (optional)
     *   - password: string (optional)
     *   - active: bool (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string $domain, string $username, array $config = []): array
    {
        $config['username'] = $username;
        return $this->client->put("{$this->basePath}/domain-admin/{$domain}", $config);
    }

    /**
     * Delete a domain admin
     *
     * @param string $domain Domain name
     * @param string $username Admin username
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $domain, string $username): array
    {
        return $this->client->delete("{$this->basePath}/domain-admin/{$domain}", [
            'username' => $username,
        ]);
    }
}

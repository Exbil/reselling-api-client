<?php

namespace Exbil\CloudApi\Mailcow;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
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
     * Get domain admins
     *
     * @param string $domain Domain name
     * @param int|null $adminId Optional admin ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string $domain, ?int $adminId = null): array
    {
        $path = "{$this->basePath}/domain-admin/{$domain}";
        if ($adminId !== null) {
            $path .= "/{$adminId}";
        }
        return $this->client->get($path);
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
     * @param string|null $password Password (min 12 chars, auto-generated if null)
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
     *   - password: string (min 12 chars, optional)
     *   - active: boolean (optional)
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

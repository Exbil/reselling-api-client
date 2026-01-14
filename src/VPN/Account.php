<?php

namespace Exbil\CloudApi\VPN;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Account
{
    private Client $client;
    private string $basePath = 'v1/products/vpn';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all VPN accounts
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get("{$this->basePath}/accounts");
    }

    /**
     * Get a specific VPN account
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $id): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$id}");
    }

    /**
     * Create a new VPN account
     *
     * @param string $username Username (3-50 chars, alphanumeric/hyphen/underscore)
     * @param string|null $password Password (6-50 chars, auto-generated if null)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $username, ?string $password = null): array
    {
        $data = ['username' => $username];
        if ($password !== null) {
            $data['password'] = $password;
        }
        return $this->client->post("{$this->basePath}/accounts", $data);
    }

    /**
     * Delete a VPN account
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $id): array
    {
        return $this->client->delete("{$this->basePath}/accounts/{$id}");
    }

    /**
     * Synchronize account from external API
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function sync(int $id): array
    {
        return $this->client->post("{$this->basePath}/accounts/{$id}/sync");
    }

    /**
     * Change VPN account password
     *
     * @param int $id Account ID
     * @param string $password New password (6-50 chars)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function changePassword(int $id, string $password): array
    {
        return $this->client->put("{$this->basePath}/accounts/{$id}/password", [
            'password' => $password,
        ]);
    }
}

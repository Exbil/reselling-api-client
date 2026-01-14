<?php

namespace Exbil\CloudApi\Domain;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Handle
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get handle types
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getTypes(): array
    {
        return $this->client->get('v1/domains/handles/types');
    }

    /**
     * Get all handles
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get('v1/domains/handles');
    }

    /**
     * Get a specific handle
     *
     * @param string|int $handleId Handle ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string|int $handleId): array
    {
        return $this->client->get("v1/domains/handles/{$handleId}");
    }

    /**
     * Create a new handle
     *
     * @param array $data Handle data:
     *   - type: string (e.g., "person", "org")
     *   - firstname: string
     *   - lastname: string
     *   - organization: string (optional)
     *   - street: string
     *   - city: string
     *   - zip: string
     *   - country: string (ISO 2-letter code)
     *   - phone: string
     *   - email: string
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $data): array
    {
        return $this->client->post('v1/domains/handles', $data);
    }

    /**
     * Update a handle
     *
     * @param string|int $handleId Handle ID
     * @param array $data Handle data to update
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(string|int $handleId, array $data): array
    {
        return $this->client->put("v1/domains/handles/{$handleId}", $data);
    }

    /**
     * Delete a handle
     *
     * @param string|int $handleId Handle ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string|int $handleId): array
    {
        return $this->client->delete("v1/domains/handles/{$handleId}");
    }

    /**
     * Set a handle as default
     *
     * @param string|int $handleId Handle ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function setDefault(string|int $handleId): array
    {
        return $this->client->post("v1/domains/handles/{$handleId}/default");
    }
}

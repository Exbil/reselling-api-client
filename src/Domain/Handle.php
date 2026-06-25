<?php

namespace Exbil\ResellingAPI\Domain;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Handle
{
    private Client $client;
    private string $basePath = 'v1/products/domains';

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
        return $this->client->get("{$this->basePath}/handles/types");
    }

    /**
     * Get all handles
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get("{$this->basePath}/handles");
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
        return $this->client->get("{$this->basePath}/handles/{$handleId}");
    }

    /**
     * Create a new handle
     *
     * @param array $data Handle data:
     *   - contact_type: string (required)
     *   - first_name: string (required)
     *   - last_name: string (required)
     *   - email: string (required)
     *   - phone: string (required)
     *   - street: string (required)
     *   - postal_code: string (required)
     *   - city: string (required)
     *   - country_code: string (required, ISO 2-letter code)
     *   - name, organization, fax, house_number, state,
     *     organization_number, vat_number, is_default (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $data): array
    {
        return $this->client->post("{$this->basePath}/handles", $data);
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
        return $this->client->put("{$this->basePath}/handles/{$handleId}", $data);
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
        return $this->client->delete("{$this->basePath}/handles/{$handleId}");
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
        return $this->client->post("{$this->basePath}/handles/{$handleId}/default");
    }
}

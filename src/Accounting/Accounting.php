<?php

namespace Exbil\CloudApi\Accounting;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Accounting
{
    private Client $client;
    private string $basePath = 'v1/accounting';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get team/user billing information
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getUserData(): array
    {
        return $this->client->get("{$this->basePath}/user-data");
    }

    /**
     * Get current credit status
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getCreditStatus(): array
    {
        return $this->client->get("{$this->basePath}/credit-status");
    }

    /**
     * Get current month usage summary
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getUsage(): array
    {
        return $this->client->get("{$this->basePath}/usage");
    }

    /**
     * Get detailed usage records with optional filters
     *
     * @param array $filters Available filters:
     *   - start: string (Y-m-d)
     *   - end: string (Y-m-d)
     *   - product_type: string
     *   - limit: int
     *   - offset: int
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getUsageDetails(array $filters = []): array
    {
        return $this->client->get("{$this->basePath}/usage/details", $filters);
    }

    /**
     * Get all invoices
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getInvoices(): array
    {
        return $this->client->get("{$this->basePath}/invoices");
    }

    /**
     * Get a specific invoice by ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getInvoice(int $id): array
    {
        return $this->client->get("{$this->basePath}/invoices/{$id}");
    }
}

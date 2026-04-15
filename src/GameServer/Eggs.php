<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Eggs
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver/eggs';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get all available game eggs
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get($this->basePath);
    }

    /**
     * Get a specific egg by slug
     *
     * @param string $slug Egg slug identifier
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $slug): array
    {
        return $this->client->get("{$this->basePath}/{$slug}");
    }
}

<?php

namespace Exbil\ResellingAPI\TeamSpeak;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Channels
{
    private Client $client;
    private string $basePath = 'v1/products/teamspeak';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a channel (optionally as a sub-channel of $parentId).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(int $id, string $name, int $parentId = 0): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/channels", [
            'name' => $name,
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Delete a channel by its channel id (cid).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $id, int $cid): array
    {
        return $this->client->delete("{$this->basePath}/servers/{$id}/channels/{$cid}");
    }
}

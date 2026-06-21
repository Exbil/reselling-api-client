<?php

namespace Exbil\ResellingAPI\TeamSpeak;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Security
{
    private Client $client;
    private string $basePath = 'v1/products/teamspeak';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a privilege key (token) for a server group.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createToken(int $id, int $serverGroupId, string $description = ''): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/tokens", [
            'server_group_id' => $serverGroupId,
            'description' => $description,
        ]);
    }

    /**
     * Delete a privilege key (token).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteToken(int $id, string $token): array
    {
        return $this->client->delete("{$this->basePath}/servers/{$id}/tokens", [
            'token' => $token,
        ]);
    }

    /**
     * Add a ban rule by IP and/or client UID.
     *
     * @param array{ip?:string,uid?:string,seconds?:int,reason?:string} $rule
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function addBan(int $id, array $rule): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/bans", $rule);
    }

    /**
     * Remove a ban by its ban id.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function removeBan(int $id, int $banId): array
    {
        return $this->client->delete("{$this->basePath}/servers/{$id}/bans", [
            'banid' => $banId,
        ]);
    }
}

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

    // ==================== TOKENS ====================

    /**
     * List all privilege keys (tokens) of the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getTokens(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/tokens");
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
     * Create a privilege key (token) via the dedicated endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createTokenDetailed(int $id, int $type, int $groupId, string $description = ''): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/tokens/create", [
            'type' => $type,
            'group_id' => $groupId,
            'description' => $description,
        ]);
    }

    /**
     * Delete a privilege key (token).
     *
     * @param array $data Optional payload (e.g. ['token' => '...']).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteToken(int $id, array $data = []): array
    {
        return $this->client->delete("{$this->basePath}/servers/{$id}/tokens", $data);
    }

    /**
     * Delete a privilege key (token) via the POST endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteTokenPost(int $id, string $token): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/tokens/delete", [
            'token' => $token,
        ]);
    }

    // ==================== BANS ====================

    /**
     * List all ban rules of the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getBans(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/bans");
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
     * Create a ban rule via the dedicated endpoint.
     *
     * @param array{ip?:string,name?:string,uid?:string,duration?:int,reason?:string} $rule
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createBan(int $id, array $rule): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/bans/create", $rule);
    }

    /**
     * Remove a ban by its ban id (DELETE endpoint).
     *
     * @param array $data Optional payload (e.g. ['banid' => 1]).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function removeBan(int $id, array $data = []): array
    {
        return $this->client->delete("{$this->basePath}/servers/{$id}/bans", $data);
    }

    /**
     * Delete a ban by its ban id via the POST endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteBan(int $id, int $banId): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/bans/{$banId}/delete");
    }

    /**
     * Remove all ban rules from the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function clearBans(int $id): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/bans/clear");
    }
}

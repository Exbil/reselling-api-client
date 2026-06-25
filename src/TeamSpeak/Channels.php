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
     * List all channels of the virtual server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/channels");
    }

    /**
     * Get a single channel by its channel id (cid).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $id, int $cid): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/channels/{$cid}");
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
     * Create a channel with the full set of options.
     *
     * @param array{
     *   parent_id?:int,password?:string,max_clients?:int,topic?:string,
     *   description?:string,codec?:int,codec_quality?:int,
     *   is_permanent?:bool,is_semi_permanent?:bool
     * } $options
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createDetailed(int $id, string $name, array $options = []): array
    {
        return $this->client->post(
            "{$this->basePath}/servers/{$id}/channels/create",
            array_merge(['name' => $name], $options)
        );
    }

    /**
     * Update a channel by its channel id (cid).
     *
     * @param array{
     *   password?:string,max_clients?:int,topic?:string,description?:string,
     *   codec?:int,codec_quality?:int,is_permanent?:bool,is_semi_permanent?:bool
     * } $options
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(int $id, int $cid, string $name, array $options = []): array
    {
        return $this->client->patch(
            "{$this->basePath}/servers/{$id}/channels/{$cid}",
            array_merge(['name' => $name], $options)
        );
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

    /**
     * Delete a channel by its channel id (cid) via the POST endpoint.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deletePost(int $id, int $cid, bool $force = false): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/channels/{$cid}/delete", [
            'force' => $force,
        ]);
    }
}

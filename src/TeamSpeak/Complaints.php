<?php

namespace Exbil\ResellingAPI\TeamSpeak;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Complaints
{
    private Client $client;
    private string $basePath = 'v1/products/teamspeak';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List all complaints of the virtual server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/complaints");
    }

    /**
     * Delete all complaints against a target client database id.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteAll(int $id, int $tcldbid): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/complaints/{$tcldbid}/delete-all");
    }

    /**
     * Delete a single complaint from $fcldbid against $tcldbid.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $id, int $tcldbid, int $fcldbid): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/complaints/{$tcldbid}/{$fcldbid}/delete");
    }
}

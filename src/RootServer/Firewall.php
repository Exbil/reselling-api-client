<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Firewall
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the firewall rules for a server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getRules(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/firewall/rules", $query);
    }

    /**
     * Create a firewall rule
     *
     * @param int $server Server ID
     * @param array $data Rule body
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createRule(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/firewall/rules/create", $data);
    }

    /**
     * Update a firewall rule
     *
     * @param int $server Server ID
     * @param int|string $rule Rule ID
     * @param array $data Rule body
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateRule(int $server, int|string $rule, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/firewall/rules/{$rule}/update", $data);
    }

    /**
     * Delete a firewall rule
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteRule(int $server, int|string $rule): array
    {
        return $this->client->post("{$this->basePath}/{$server}/firewall/rules/{$rule}/delete");
    }

    /**
     * Reorder the firewall rules
     *
     * @param int $server Server ID
     * @param array $order Ordered list of rule IDs
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reorderRules(int $server, array $order, array $extra = []): array
    {
        $data = array_merge($extra, ['order' => $order]);
        return $this->client->post("{$this->basePath}/{$server}/firewall/rules/reorder", $data);
    }

    /**
     * Apply a firewall preset to a server
     *
     * @param int $server Server ID
     * @param string $preset Preset identifier
     * @param bool|null $replace Whether to replace existing rules
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function applyPreset(int $server, string $preset, ?bool $replace = null, array $extra = []): array
    {
        $data = $extra;
        $data['preset'] = $preset;
        if ($replace !== null) {
            $data['replace'] = $replace;
        }
        return $this->client->post("{$this->basePath}/{$server}/firewall/apply", $data);
    }
}

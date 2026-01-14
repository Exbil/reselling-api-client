<?php

namespace Exbil\CloudApi\RootServer;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class RootServer
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';
    private ?Location $locationHandler = null;
    private ?Cluster $clusterHandler = null;
    private ?Power $powerHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== SERVER LISTING ====================

    /**
     * Get all root servers with optional filters
     *
     * @param array $filters Available: state, datacenter_id, cluster_id, team_id
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(array $filters = []): array
    {
        return $this->client->get($this->basePath, $filters);
    }

    /**
     * Get a specific root server by VM ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $vmId): array
    {
        return $this->client->get("{$this->basePath}/{$vmId}");
    }

    // ==================== SERVER CREATION & MODIFICATION ====================

    /**
     * Create a new root server
     *
     * @param string $clusterSlug Cluster slug
     * @param array $config Server configuration:
     *   - hostname/server_name: string
     *   - cores: int
     *   - ram_mb/ram: int (MB)
     *   - disk_gb/disk: int (GB)
     *   - root_server_os_version_id/operating_system/operating_system_slug: OS selection
     *   - root_password/admin_password: string (optional)
     *   - ssh_keys: array (optional)
     *   - login_method: string (optional)
     *   - backup_slots: int (optional)
     *   - ipv4_addresses/ipv4_count/ipv4: int (optional)
     *   - ipv6_addresses/ipv6_count/ipv6: int (optional)
     *   - user_data/first_run_script: string (optional)
     *   - boot: bool (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $clusterSlug, array $config): array
    {
        return $this->client->post("{$this->basePath}/cluster/{$clusterSlug}/create", $config);
    }

    /**
     * Update/resize a root server
     *
     * @param int $vmId Server VM ID
     * @param array $config Resize options: cores, ram_mb, disk_gb (disk can only increase)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function update(int $vmId, array $config): array
    {
        return $this->client->put("{$this->basePath}/{$vmId}", $config);
    }

    /**
     * Delete a root server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $vmId): array
    {
        return $this->client->delete("{$this->basePath}/{$vmId}");
    }

    /**
     * Reset root password
     *
     * @param int $vmId Server VM ID
     * @param string|null $password New password (auto-generated if null)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function resetRootPassword(int $vmId, ?string $password = null): array
    {
        $data = [];
        if ($password !== null) {
            $data['password'] = $password;
        }
        return $this->client->post("{$this->basePath}/{$vmId}/reset-root-password", $data);
    }

    /**
     * Reinstall a root server
     *
     * @param int $vmId Server VM ID
     * @param array $config Reinstall options:
     *   - server_name: string (optional)
     *   - cores, ram, disk: int (optional)
     *   - root_server_os_version_id/operating_system/operating_system_slug: OS (optional)
     *   - admin_password: string (optional)
     *   - ssh_keys: array (optional)
     *   - first_run_script: string (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reinstall(int $vmId, array $config = []): array
    {
        return $this->client->post("{$this->basePath}/{$vmId}/reinstall", $config);
    }

    // ==================== MONITORING & STATUS ====================

    /**
     * Get live server stats
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getStats(int $vmId): array
    {
        return $this->client->get("{$this->basePath}/{$vmId}/stats");
    }

    /**
     * Get server logs
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getLogs(int $vmId, int $limit = 50): array
    {
        return $this->client->get("{$this->basePath}/{$vmId}/logs", ['limit' => $limit]);
    }

    /**
     * Get server tasks
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getTasks(int $vmId, int $limit = 50): array
    {
        return $this->client->get("{$this->basePath}/{$vmId}/tasks", ['limit' => $limit]);
    }

    /**
     * Location Management
     */
    public function location(): Location
    {
        return $this->locationHandler ??= new Location($this->client);
    }

    /**
     * Cluster Management
     */
    public function cluster(): Cluster
    {
        return $this->clusterHandler ??= new Cluster($this->client);
    }

    /**
     * Power Control
     */
    public function power(): Power
    {
        return $this->powerHandler ??= new Power($this->client);
    }
}

<?php

namespace Exbil\ResellingAPI\RootServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class RootServer
{
    private Client $client;
    private string $basePath = 'v1/products/rootserver';
    private ?Location $locationHandler = null;
    private ?Cluster $clusterHandler = null;
    private ?Power $powerHandler = null;
    private ?Firewall $firewallHandler = null;
    private ?Backup $backupHandler = null;
    private ?Rdns $rdnsHandler = null;
    private ?Vpc $vpcHandler = null;
    private ?Ddos $ddosHandler = null;
    private ?Task $taskHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== SERVER LISTING ====================

    /**
     * Get all root servers with optional filters
     *
     * @param array $query Optional query filters
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(array $query = []): array
    {
        return $this->client->get($this->basePath, $query);
    }

    /**
     * Get a specific root server by ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $server): array
    {
        return $this->client->get("{$this->basePath}/{$server}");
    }

    // ==================== SERVER CREATION & MODIFICATION ====================

    /**
     * Create a new root server in the given cluster
     *
     * Documented body fields: server_name, cores, ram, disk, operating_system
     * (required), plus many optional fields (team_id, datacenter_id, cluster_id,
     * node_id, operating_system_slug, admin_password, ssh_keys, first_run_script,
     * interfaces, root_server_os_version_id, ssh_key_id, hostname, login_method,
     * root_username, root_password, cpu_type, cpu_platform, ram_mb, disk_gb,
     * backup_slots, free_traffic_gb, net_limit_mbit, user_data, boot,
     * ipv4_addresses, ipv6_addresses, ipv4_count, ipv6_count, ipv4, ipv6).
     *
     * @param string $cluster Cluster slug or ID
     * @param array $data Server configuration body
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $cluster, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/cluster/{$cluster}/create", $data);
    }

    /**
     * Resize a root server
     *
     * @param int $server Server ID
     * @param int|null $cores
     * @param int|null $ramMb
     * @param int|null $diskGb Disk can only be increased
     * @param string|null $cpuType
     * @param array $extra Additional body fields
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function resize(
        int $server,
        ?int $cores = null,
        ?int $ramMb = null,
        ?int $diskGb = null,
        ?string $cpuType = null,
        array $extra = []
    ): array {
        $data = $extra;
        if ($cores !== null) {
            $data['cores'] = $cores;
        }
        if ($ramMb !== null) {
            $data['ram_mb'] = $ramMb;
        }
        if ($diskGb !== null) {
            $data['disk_gb'] = $diskGb;
        }
        if ($cpuType !== null) {
            $data['cpu_type'] = $cpuType;
        }
        return $this->client->post("{$this->basePath}/{$server}/resize", $data);
    }

    /**
     * Delete a root server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $server): array
    {
        return $this->client->post("{$this->basePath}/{$server}/delete");
    }

    /**
     * Reset the root password (auto-generated if no body is provided)
     *
     * @param int $server Server ID
     * @param array $data Optional body
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function resetRootPassword(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/reset-root-password", $data);
    }

    /**
     * Reinstall a root server
     *
     * Documented body fields: server_name, cores, ram, disk,
     * root_server_os_version_id, operating_system, operating_system_slug,
     * admin_password, ssh_keys, first_run_script.
     *
     * @param int $server Server ID
     * @param array $data Reinstall configuration body
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function reinstall(int $server, array $data = []): array
    {
        return $this->client->post("{$this->basePath}/{$server}/reinstall", $data);
    }

    // ==================== MONITORING & STATUS ====================

    /**
     * Get live server stats
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getLiveStats(int $server): array
    {
        return $this->client->get("{$this->basePath}/{$server}/live-stats");
    }

    /**
     * Get historical server stats
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getHistoricalStats(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/historical-stats", $query);
    }

    /**
     * Get server logs
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getLogs(int $server, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/{$server}/logs", $query);
    }

    /**
     * Get the serial console connection details
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getSerial(int $server): array
    {
        return $this->client->get("{$this->basePath}/{$server}/serial");
    }

    /**
     * Get the VNC console connection details
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getVnc(int $server): array
    {
        return $this->client->get("{$this->basePath}/{$server}/vnc");
    }

    // ==================== ROOT-LEVEL RESOURCES ====================

    /**
     * Get all available OS versions (across every cluster)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getOsList(): array
    {
        return $this->client->get("{$this->basePath}/os-list");
    }

    /**
     * Get the available firewall presets
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getFirewallPresets(): array
    {
        return $this->client->get("{$this->basePath}/firewall-presets");
    }

    // ==================== SUB-RESOURCE ACCESSORS ====================

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

    /**
     * Firewall Management
     */
    public function firewall(): Firewall
    {
        return $this->firewallHandler ??= new Firewall($this->client);
    }

    /**
     * Backup Management
     */
    public function backup(): Backup
    {
        return $this->backupHandler ??= new Backup($this->client);
    }

    /**
     * Reverse DNS Management
     */
    public function rdns(): Rdns
    {
        return $this->rdnsHandler ??= new Rdns($this->client);
    }

    /**
     * VPC Management
     */
    public function vpc(): Vpc
    {
        return $this->vpcHandler ??= new Vpc($this->client);
    }

    /**
     * DDoS Information
     */
    public function ddos(): Ddos
    {
        return $this->ddosHandler ??= new Ddos($this->client);
    }

    /**
     * Task Management
     */
    public function task(): Task
    {
        return $this->taskHandler ??= new Task($this->client);
    }
}

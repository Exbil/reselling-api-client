<?php

namespace Exbil\ResellingAPI\TeamSpeak;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class TeamSpeak
{
    private Client $client;
    private string $basePath = 'v1/products/teamspeak';
    private ?Channels $channelsHandler = null;
    private ?Clients $clientsHandler = null;
    private ?Security $securityHandler = null;
    private ?Backups $backupsHandler = null;
    private ?Complaints $complaintsHandler = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== INFORMATION ====================

    /**
     * Get pricing and available locations (datacenters with free capacity).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPricing(): array
    {
        return $this->client->get("{$this->basePath}/pricing");
    }

    /**
     * Get the raw price list.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPrices(): array
    {
        return $this->client->get("{$this->basePath}/prices");
    }

    // ==================== SERVERS ====================

    /**
     * List all TeamSpeak servers of the team.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get("{$this->basePath}/servers");
    }

    /**
     * Get a single TeamSpeak server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}");
    }

    /**
     * Order a new TeamSpeak server.
     *
     * @param array{name:string,slots:int,datacenter_id?:int} $config
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $config): array
    {
        return $this->client->post("{$this->basePath}/servers", $config);
    }

    /**
     * Order a new TeamSpeak server (alternative endpoint).
     *
     * @param array{name:string,slots:int,datacenter_id?:int} $config
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createAlt(array $config): array
    {
        return $this->client->post("{$this->basePath}/servers/create", $config);
    }

    /**
     * Convenience helper to order a server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function order(string $name, int $slots, ?int $datacenterId = null): array
    {
        $config = ['name' => $name, 'slots' => $slots];
        if ($datacenterId !== null) {
            $config['datacenter_id'] = $datacenterId;
        }

        return $this->create($config);
    }

    /**
     * Request deletion of a TeamSpeak server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $id): array
    {
        return $this->client->delete("{$this->basePath}/servers/{$id}");
    }

    /**
     * Request deletion of a TeamSpeak server (alternative endpoint).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteAlt(int $id): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/delete");
    }

    /**
     * Live view: server info, channels and connected clients.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function view(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/view");
    }

    /**
     * Advanced data: server groups, privilege tokens, bans, complaints and log.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function extras(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/extras");
    }

    /**
     * Get live statistics for the virtual server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stats(int $id): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/stats");
    }

    /**
     * Get historical statistics for the virtual server.
     *
     * @param array $query Optional filters.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function statsHistory(int $id, array $query = []): array
    {
        return $this->client->get("{$this->basePath}/servers/{$id}/stats/history", $query);
    }

    // ==================== POWER ====================

    /**
     * Start the virtual server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function start(int $id): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/start");
    }

    /**
     * Stop the virtual server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function stop(int $id): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/stop");
    }

    // ==================== SETTINGS ====================

    /**
     * Up-/downgrade the slot count (subject to the host's available capacity).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function resize(int $id, int $slots): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/resize", [
            'slots' => $slots,
        ]);
    }

    /**
     * Upgrade the slot count of the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function upgrade(int $id, int $slots): array
    {
        return $this->client->patch("{$this->basePath}/servers/{$id}/upgrade", [
            'slots' => $slots,
        ]);
    }

    /**
     * Update server settings (name, password, messages).
     *
     * @param array{name:string,password?:string,welcome_message?:string,host_message?:string} $settings
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateSettings(int $id, array $settings): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/settings", $settings);
    }

    /**
     * Patch server settings (name, welcome message, host banner).
     *
     * @param array{name?:string,welcome_message?:string,hostbanner_url?:string,hostbanner_gfx_url?:string} $settings
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function patchSettings(int $id, array $settings = []): array
    {
        return $this->client->patch("{$this->basePath}/servers/{$id}/settings", $settings);
    }

    /**
     * Send a text message to every connected client.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function broadcast(int $id, string $message): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/message", [
            'message' => $message,
        ]);
    }

    /**
     * Kick a client from the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function kick(int $id, int $clid, string $reason = '', array $extra = []): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/kick", array_merge([
            'clid' => $clid,
            'reason' => $reason,
        ], $extra));
    }

    /**
     * Ban a connected client.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function ban(int $id, int $clid, int $seconds = 3600, string $reason = '', array $extra = []): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/ban", array_merge([
            'clid' => $clid,
            'seconds' => $seconds,
            'reason' => $reason,
        ], $extra));
    }

    // ==================== TSDNS ====================

    /**
     * Set the TSDNS hostname for the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function setTsdns(int $id, string $hostname): array
    {
        return $this->client->patch("{$this->basePath}/servers/{$id}/tsdns", [
            'hostname' => $hostname,
        ]);
    }

    /**
     * Remove the TSDNS hostname from the server.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function removeTsdns(int $id): array
    {
        return $this->client->post("{$this->basePath}/servers/{$id}/tsdns/remove");
    }

    // ==================== SUB-HANDLERS ====================

    /**
     * Channel management (create, delete).
     */
    public function channels(): Channels
    {
        return $this->channelsHandler ??= new Channels($this->client);
    }

    /**
     * Connected client management (move, kick, ban, details).
     */
    public function clients(): Clients
    {
        return $this->clientsHandler ??= new Clients($this->client);
    }

    /**
     * Security: privilege tokens and bans.
     */
    public function security(): Security
    {
        return $this->securityHandler ??= new Security($this->client);
    }

    /**
     * Snapshot backups (download, restore).
     */
    public function backups(): Backups
    {
        return $this->backupsHandler ??= new Backups($this->client);
    }

    /**
     * Complaint management.
     */
    public function complaints(): Complaints
    {
        return $this->complaintsHandler ??= new Complaints($this->client);
    }
}

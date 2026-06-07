<?php

namespace Exbil\ResellingAPI\CloudServices;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Backups
{
    private Client $client;
    private string $basePath = 'v1/products/cloudservices';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List backups for a service.
     *
     * Returns the daemon-side row payload including `team_id`,
     * `cloudservice_id`, `origin` ('created' / 'uploaded'),
     * `disk_usage`, `is_locked` and `completed_at`.
     *
     * @param string $uuid Service UUID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/backups");
    }

    /**
     * Create a new backup. The daemon snapshots the current
     * cloudservice_id + team_id onto the row so a later restore
     * can enforce both guards.
     *
     * @param string      $uuid          Service UUID
     * @param string|null $name          Optional backup name
     * @param array       $ignoredFiles  Optional list of glob patterns to exclude
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(string $uuid, ?string $name = null, array $ignoredFiles = []): array
    {
        $data = [];
        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($ignoredFiles !== []) {
            $data['ignored_files'] = $ignoredFiles;
        }
        return $this->client->post("{$this->basePath}/{$uuid}/backups", $data);
    }

    /**
     * Upload a previously-downloaded backup archive. The file must
     * be a `.tar.gz` produced by this platform — the daemon rejects
     * anything that doesn't carry the ECB1 magic (current encrypted
     * format) or the gzip 0x1f8b magic (legacy plaintext).
     *
     * Counts against the per-server slot quota (default 3) and the
     * size cap (default 100 GB). Daemon refuses with 422 / 413 / 415
     * respectively.
     *
     * @param string $uuid     Service UUID
     * @param string $filePath Absolute local path to the .tar.gz archive
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function upload(string $uuid, string $filePath): array
    {
        return $this->client->request('POST', "{$this->basePath}/{$uuid}/backups/upload", [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => fopen($filePath, 'rb'),
                    'filename' => basename($filePath),
                ],
            ],
        ]);
    }

    /**
     * Delete a backup. Triggers the deletion certificate notification
     * to the account owner (DSGVO Art. 17 paper trail).
     *
     * @param string $uuid     Service UUID
     * @param string $backupId Backup UUID or numeric ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $uuid, string $backupId): array
    {
        return $this->client->delete("{$this->basePath}/{$uuid}/backups/{$backupId}");
    }

    /**
     * Restore a backup. The daemon performs stop → extract → chown →
     * auto-start in sequence.
     *
     * Options:
     *   - switch_template (bool, default true): when the backup's
     *     cloudservice_id differs from the server's, flip the
     *     server's template (cloudservice_id + docker_image) to
     *     match the backup before extracting. Set to false to refuse
     *     cross-template restores with 409.
     *   - force (bool, default false): bypass both the cross-team
     *     guard and the cross-template guard. Operator recovery
     *     only — wrong volume content can crash the container.
     *
     * @param string $uuid     Service UUID
     * @param string $backupId Backup UUID or numeric ID
     * @param array  $options  ['switch_template' => bool, 'force' => bool]
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function restore(string $uuid, string $backupId, array $options = []): array
    {
        $payload = [];
        if (array_key_exists('switch_template', $options)) {
            $payload['switch_template'] = (bool) $options['switch_template'];
        }
        if (!empty($options['force'])) {
            $payload['force'] = true;
        }
        return $this->client->post("{$this->basePath}/{$uuid}/backups/{$backupId}/restore", $payload);
    }

    /**
     * Toggle the lock flag on a backup. Locked backups are excluded
     * from retention sweeps until unlocked.
     *
     * @param string $uuid     Service UUID
     * @param string $backupId Backup UUID or numeric ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function toggleLock(string $uuid, string $backupId): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/backups/{$backupId}/lock");
    }

    /**
     * Download a backup archive directly to a local file. Stream-
     * chunked at 8 KB so multi-GB archives don't sit in memory.
     *
     * Returns the saved file path. The downloaded file is an
     * encrypted .tar.gz (ECB1 magic) — keep it as-is for re-upload
     * via {@see upload()}. Decryption is not exposed in the SDK;
     * the same daemon's BACKUP_ENCRYPTION_KEY is required.
     *
     * @param string $uuid       Service UUID
     * @param string $backupId   Backup UUID or numeric ID
     * @param string $targetPath Absolute path the archive will be written to
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function download(string $uuid, string $backupId, string $targetPath): string
    {
        $url = ltrim("{$this->basePath}/{$uuid}/backups/{$backupId}/download", '/');
        $resp = $this->client->getHttpClient()->request('GET', $url, ['stream' => true]);
        $body = $resp->getBody();
        $out = fopen($targetPath, 'wb');
        if ($out === false) {
            throw new ApiException("Could not open {$targetPath} for writing", 0);
        }
        while (!$body->eof()) {
            $chunk = $body->read(8192);
            if ($chunk === '') {
                break;
            }
            fwrite($out, $chunk);
        }
        fclose($out);
        return $targetPath;
    }
}

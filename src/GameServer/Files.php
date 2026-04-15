<?php

namespace Exbil\ResellingAPI\GameServer;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Files
{
    private Client $client;
    private string $basePath = 'v1/products/gameserver';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List files in a directory
     *
     * @param int $serverId Server ID
     * @param string $dir Directory path
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function list(int $serverId, string $dir = '/'): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/files/list", [
            'directory' => $dir,
        ]);
    }

    /**
     * Read file contents
     *
     * @param int $serverId Server ID
     * @param string $file File path
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function read(int $serverId, string $file): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/files/read", [
            'file' => $file,
        ]);
    }

    /**
     * Write content to a file
     *
     * @param int $serverId Server ID
     * @param string $file File path
     * @param string $content File content
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function write(int $serverId, string $file, string $content): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/files/write", [
            'file' => $file,
            'content' => $content,
        ]);
    }

    /**
     * Upload a file to the server
     *
     * @param int $serverId Server ID
     * @param string $filePath Local file path to upload
     * @param string $dir Target directory on the server
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function upload(int $serverId, string $filePath, string $dir = '/'): array
    {
        return $this->client->request('POST', "{$this->basePath}/{$serverId}/files/upload", [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
                [
                    'name' => 'directory',
                    'contents' => $dir,
                ],
            ],
        ]);
    }

    /**
     * Download a file from the server
     *
     * @param int $serverId Server ID
     * @param string $file File path
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function download(int $serverId, string $file): array
    {
        return $this->client->get("{$this->basePath}/{$serverId}/files/download", [
            'file' => $file,
        ]);
    }

    /**
     * Delete files
     *
     * @param int $serverId Server ID
     * @param array $files Array of file paths to delete
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(int $serverId, array $files): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/files/delete", [
            'files' => $files,
        ]);
    }

    /**
     * Rename/move a file
     *
     * @param int $serverId Server ID
     * @param string $from Source path
     * @param string $to Destination path
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function rename(int $serverId, string $from, string $to): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/files/rename", [
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Compress files into an archive
     *
     * @param int $serverId Server ID
     * @param array $files Array of file paths to compress
     * @param string $output Output archive path
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function compress(int $serverId, array $files, string $output): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/files/compress", [
            'files' => $files,
            'output' => $output,
        ]);
    }

    /**
     * Decompress an archive
     *
     * @param int $serverId Server ID
     * @param string $file Archive file path
     * @param string $target Target directory
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function decompress(int $serverId, string $file, string $target): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/files/decompress", [
            'file' => $file,
            'target' => $target,
        ]);
    }

    /**
     * Create a directory
     *
     * @param int $serverId Server ID
     * @param string $path Directory path to create
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function mkdir(int $serverId, string $path): array
    {
        return $this->client->post("{$this->basePath}/{$serverId}/files/mkdir", [
            'path' => $path,
        ]);
    }
}

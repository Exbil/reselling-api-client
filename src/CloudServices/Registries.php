<?php

namespace Exbil\ResellingAPI\CloudServices;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Container Registries — the customer-owned private Docker
 * registries hosted on the same nodes as cloud services. Each
 * registry gets a `<namespace>.{node-domain}` URL and a generated
 * admin password the customer uses to `docker login` and push
 * images to it.
 *
 * Packages map to fixed storage / repo / robot quotas (see
 * `packages()`); custom mode lets resellers compose their own
 * resource bundle. Pricing is hourly on usage above the package
 * floor — call `calculatePrice()` for live preview.
 */
class Registries
{
    private Client $client;
    private string $basePath = 'v1/products/cloudservices/registries';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List the calling team's registries.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAll(): array
    {
        return $this->client->get($this->basePath);
    }

    /**
     * Show a single registry by UUID. Returns the daemon-side
     * health snapshot, current storage usage, and the admin
     * credentials only when the caller's API key has MANAGE
     * permission (VIEW-only keys see the metadata).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}");
    }

    /**
     * Order a new container registry.
     *
     * Modes:
     *   - 'package': pick a pre-defined slug from {@see packages()}.
     *     Required: `package_slug`.
     *   - 'custom':  compose resources manually. Required:
     *     `storage_gb`, `repo_limit`, `robot_limit`.
     *
     * Always required:
     *   - `namespace`: 1-31 chars, lowercase alphanumeric + dashes,
     *     starts with a letter. Becomes the subdomain (after a
     *     server-generated `r-` prefix on the wider URL).
     *
     * Optional:
     *   - `auto_upgrade`: roll the registry image forward on
     *     security patches (default true on packages).
     *   - `node_id`: pin placement to a specific node. Defaults to
     *     the first non-maintenance node.
     *
     * @param array $config
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function create(array $config): array
    {
        return $this->client->post($this->basePath, $config);
    }

    /**
     * Delete a registry. The daemon's sharedregistry module
     * secure-wipes the namespace's blobs before releasing the slot
     * (AVV / DSGVO requirement).
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $uuid): array
    {
        return $this->client->delete("{$this->basePath}/{$uuid}");
    }

    /**
     * Available registry packages and their hourly + monthly prices.
     * Returns the catalogue the order form should render: package
     * slug, included storage / repos / robots, hourly cents, included
     * Boolean and a marketing blurb.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function packages(): array
    {
        return $this->client->get("{$this->basePath}/packages");
    }

    /**
     * Live price preview for the order form. Same payload shape as
     * {@see create()} minus `namespace` + `node_id`; returns the
     * hourly / monthly numbers so customers see the total before
     * committing.
     *
     * @param array $payload See create() — mode + package_slug OR
     *                       storage_gb + repo_limit + robot_limit.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function calculatePrice(array $payload): array
    {
        return $this->client->post("{$this->basePath}/calculate-price", $payload);
    }

    /**
     * Check whether a desired namespace is free. Returns
     * {"available": true|false} so the form can grey out the
     * "Order" button while the customer is still typing.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function checkNamespace(string $namespace): array
    {
        return $this->client->post("{$this->basePath}/check-namespace", [
            'namespace' => $namespace,
        ]);
    }
}

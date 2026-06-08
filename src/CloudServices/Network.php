<?php

namespace Exbil\ResellingAPI\CloudServices;

use Exbil\ResellingAPI\Client;
use Exbil\ResellingAPI\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Per-server IPv6 lifecycle.
 *
 * The Reselling daemon exposes a node-wide healthcheck so consumers
 * can hide the "order another v6" CTA when the upstream provider's
 * transit is down. Existing assignments keep working — only new
 * orders are paused — and the SAME endpoint handles "nachbestellen"
 * once the healthcheck flips green again.
 *
 * @api
 */
class Network
{
    private Client $client;
    private string $basePath = 'v1/products/cloudservices';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Node-wide IPv6 status: whether the operator's prefix is configured
     * and whether the daemon's TCP probe to the upstream target is
     * currently succeeding. Returns the shape:
     *
     *   { enabled: bool, prefix?: string, max_per_server?: int,
     *     healthcheck?: { healthy: bool, target: string,
     *                     last_checked_at: string, last_error?: string },
     *     reason?: string }
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function status(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/network/status");
    }

    /**
     * List addresses currently bound to the server, with the per-server
     * cap (typically 4). Primary (first allocated, marked is_primary=1)
     * is sorted first.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function listIpv6(string $uuid): array
    {
        return $this->client->get("{$this->basePath}/{$uuid}/network/ipv6");
    }

    /**
     * Order one IPv6 against the server. Returns HTTP 503 with a
     * structured "ipv6_transit_down" body when the daemon's
     * healthcheck is red; consumers should surface the body's
     * `detail.last_checked_at` + `detail.last_error` to the operator
     * rather than retrying immediately.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function orderIpv6(string $uuid): array
    {
        return $this->client->post("{$this->basePath}/{$uuid}/network/ipv6");
    }

    /**
     * Release one IPv6 row by its daemon-side numeric id (not the
     * address itself). Dropping the row marked `is_primary` is allowed
     * — egress falls back to v4 only on the next container restart,
     * which the operator should communicate to the customer.
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function releaseIpv6(string $uuid, int $addressId): array
    {
        return $this->client->delete("{$this->basePath}/{$uuid}/network/ipv6/{$addressId}");
    }

    /**
     * Order N IPv6 addresses in one orchestration. Loops {@see orderIpv6}
     * up to $count times and stops early if any single call fails (the
     * caller usually wants to surface "ordered X of Y" to the operator
     * instead of partially silent-failing). Returns the array of
     * successfully-ordered allocation rows.
     *
     * Use this from the order flow when the customer ticks "include
     * IPv6" + a count on the create form — the daemon's single-shot
     * /network/ipv6 endpoint is the only primitive available, so the
     * panel needs to fan it out itself.
     *
     * Caps internally at $max so a typo doesn't burn through the entire
     * /64 prefix. Daemon enforces its own per-server limit (default 4)
     * and will reject calls past that with HTTP 422.
     *
     * @return array<int,array<string,mixed>> Ordered allocation rows.
     *
     * @throws ApiException        Wrapped from the daemon's 503/422 response.
     * @throws GuzzleException
     */
    public function orderIpv6Batch(string $uuid, int $count, int $max = 16): array
    {
        $count = max(0, min($count, $max));
        $ordered = [];
        for ($i = 0; $i < $count; $i++) {
            $resp = $this->orderIpv6($uuid);
            // The daemon returns either {data: {...row...}} or {address: ...}
            // depending on version; keep the raw response and let the caller
            // pick the shape they want.
            $ordered[] = $resp;
        }
        return $ordered;
    }
}

<?php

namespace Exbil\CloudApi;

use Exbil\CloudApi\Accounting\Accounting;
use Exbil\CloudApi\Domain\Domain;
use Exbil\CloudApi\Exceptions\ApiException;
use Exbil\CloudApi\Exceptions\AuthenticationException;
use Exbil\CloudApi\Exceptions\ForbiddenException;
use Exbil\CloudApi\Exceptions\NotFoundException;
use Exbil\CloudApi\Exceptions\ValidationException;
use Exbil\CloudApi\Mailcow\Mailcow;
use Exbil\CloudApi\RootServer\RootServer;
use Exbil\CloudApi\VPN\VPN;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client
{
    private GuzzleClient $httpClient;
    private Credentials $credentials;

    private ?Accounting $accountingHandler = null;
    private ?Domain $domainHandler = null;
    private ?RootServer $rootServerHandler = null;
    private ?VPN $vpnHandler = null;
    private ?Mailcow $mailcowHandler = null;

    public function __construct(string $apiKey, string $baseUrl = 'https://reselling-portal.de/api/', ?GuzzleClient $httpClient = null)
    {
        $this->credentials = new Credentials($apiKey, $baseUrl);
        $this->initHttpClient($httpClient);
    }

    private function initHttpClient(?GuzzleClient $httpClient = null): void
    {
        $this->httpClient = $httpClient ?: new GuzzleClient([
            'base_uri' => $this->credentials->getBaseUrl(),
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->credentials->getApiKey(),
                'User-Agent' => 'ExbilCloudApiClient/1.0',
            ],
            'timeout' => 30,
            'verify' => true,
        ]);
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getHttpClient(): GuzzleClient
    {
        return $this->httpClient;
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function request(string $method, string $endpoint, array $options = []): array
    {
        $url = ltrim($endpoint, '/');

        try {
            $response = $this->httpClient->request($method, $url, $options);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP request failed: ' . $e->getMessage(),
                $e->getCode(),
                null,
                [],
                $e
            );
        }
    }

    /**
     * @throws ApiException
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->__toString();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response: ' . json_last_error_msg());
        }

        if ($statusCode >= 200 && $statusCode < 300) {
            return $data;
        }

        $message = $data['message'] ?? 'An error occurred';
        $errors = $data['errors'] ?? [];

        match ($statusCode) {
            401 => throw new AuthenticationException($message, $statusCode, $response, $data),
            403 => throw new ForbiddenException($message, $statusCode, $response, $data),
            404 => throw new NotFoundException($message, $statusCode, $response, $data),
            422 => throw new ValidationException($message, $statusCode, $response, ['errors' => $errors]),
            default => throw new ApiException($message, $statusCode, $response, $data),
        };
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function get(string $endpoint, array $query = []): array
    {
        $options = [];
        if (!empty($query)) {
            $options['query'] = $query;
        }
        return $this->request('GET', $endpoint, $options);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * @throws ApiException
     * @throws GuzzleException
     */
    public function delete(string $endpoint, array $data = []): array
    {
        return $this->request('DELETE', $endpoint, ['json' => $data]);
    }

    /**
     * Accounting API - Billing, Invoices, Credit Status, Usage
     */
    public function accounting(): Accounting
    {
        return $this->accountingHandler ??= new Accounting($this);
    }

    /**
     * Domain API - Registration, Transfer, DNS, Nameservers, Handles
     */
    public function domain(): Domain
    {
        return $this->domainHandler ??= new Domain($this);
    }

    /**
     * Root Server API - Virtual Servers, Power Control, Statistics
     */
    public function rootServer(): RootServer
    {
        return $this->rootServerHandler ??= new RootServer($this);
    }

    /**
     * VPN API - VPN Accounts, Configurations, Servers
     */
    public function vpn(): VPN
    {
        return $this->vpnHandler ??= new VPN($this);
    }

    /**
     * Mailcow API - Domains, Mailboxes, Aliases, Domain Admins
     */
    public function mailcow(): Mailcow
    {
        return $this->mailcowHandler ??= new Mailcow($this);
    }
}

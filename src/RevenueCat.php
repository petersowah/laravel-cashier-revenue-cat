<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use PeterSowah\LaravelCashierRevenueCat\Exceptions\RevenueCatException;

class RevenueCat
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.revenuecat.com';

    protected string $projectId;

    protected HttpClient $client;

    public function __construct(string $apiKey, string $projectId)
    {
        $this->apiKey = $apiKey;
        $this->projectId = $projectId;
        $this->client = $this->createDefaultClient();
    }

    public function setClient(HttpClient $client): self
    {
        $this->client = $client;

        return $this;
    }

    protected function createDefaultClient(): HttpClient
    {
        return new HttpClient([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Platform' => 'laravel',
            ],
        ]);
    }

    public function getCustomer(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}");
    }

    public function createCustomer(string $appUserId, array $attributes = []): array
    {
        return $this->post("/v2/projects/{$this->projectId}/customers", array_merge(['app_user_id' => $appUserId], $attributes));
    }

    public function updateCustomer(string $appUserId, array $attributes): array
    {
        return $this->patch("/v2/projects/{$this->projectId}/customers/{$appUserId}", $attributes);
    }

    public function deleteCustomer(string $appUserId): array
    {
        return $this->delete("/v2/projects/{$this->projectId}/customers/{$appUserId}");
    }

    public function getOfferings(?string $appUserId = null): array
    {
        $uri = "/v2/projects/{$this->projectId}/offerings";
        if ($appUserId) {
            $uri .= "?app_user_id={$appUserId}";
        }

        return $this->get($uri);
    }

    public function getProducts(): array
    {
        return $this->get("/v2/projects/{$this->projectId}/products");
    }

    public function getCustomerHistory(string $appUserId, array $params = []): array
    {
        $uri = "/v2/projects/{$this->projectId}/customers/{$appUserId}/history";
        if (! empty($params)) {
            $uri .= '?'.http_build_query($params);
        }

        return $this->get($uri);
    }

    public function getCustomerEntitlements(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/entitlements");
    }

    public function getCustomerPurchases(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/purchases");
    }

    public function getUserSubscriptions(string $appUserId): array
    {
        $customer = $this->getCustomer($appUserId);

        return array_filter($customer['subscriber']['entitlements'] ?? [], function ($entitlement) {
            return $entitlement['is_active'] ?? false;
        });
    }

    public function getCustomerOffering(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/offerings");
    }

    public function getCustomerNonSubscriptions(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/non_subscriptions");
    }

    public function getCustomerSubscriptions(string $appUserId): array
    {
        return $this->get("/v2/projects/{$this->projectId}/customers/{$appUserId}/subscriptions");
    }

    /**
     * Get the subscription name from an entitlement or webhook event.
     *
     * @param  array  $data  Either an entitlement object or webhook event data
     */
    public function getSubscriptionName(array $data): string
    {
        // Handle webhook event data
        if (isset($data['entitlement_ids']) && is_array($data['entitlement_ids'])) {
            return $data['entitlement_ids'][0] ?? '';
        }

        // Handle entitlement object
        if (isset($data['identifier'])) {
            return $data['identifier'];
        }

        // Handle entitlement from customer response
        if (isset($data['entitlement_id'])) {
            return $data['entitlement_id'];
        }

        return '';
    }

    protected function get(string $uri): array
    {
        try {
            $response = $this->client->get($uri);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function post(string $uri, array $data = []): array
    {
        try {
            $response = $this->client->post($uri, ['json' => $data]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function patch(string $uri, array $data = []): array
    {
        try {
            $response = $this->client->patch($uri, ['json' => $data]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function delete(string $uri): array
    {
        try {
            $response = $this->client->delete($uri);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RevenueCatException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

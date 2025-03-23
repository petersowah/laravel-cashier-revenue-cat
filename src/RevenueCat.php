<?php

namespace PeterSowah\LaravelCashierRevenueCat;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use PeterSowah\LaravelCashierRevenueCat\Exceptions\RevenueCatException;

class RevenueCat
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.revenuecat.com/v1';
    protected HttpClient $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
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

    public function getSubscriber(string $appUserId): array
    {
        return $this->get("/subscribers/{$appUserId}");
    }

    public function createSubscriber(string $appUserId, array $attributes = []): array
    {
        return $this->post("/subscribers", array_merge(['app_user_id' => $appUserId], $attributes));
    }

    public function updateSubscriber(string $appUserId, array $attributes): array
    {
        return $this->post("/subscribers/{$appUserId}", $attributes);
    }

    public function deleteSubscriber(string $appUserId): array
    {
        return $this->delete("/subscribers/{$appUserId}");
    }

    public function getOfferings(string $appUserId = null): array
    {
        $uri = '/offerings';
        if ($appUserId) {
            $uri .= "?app_user_id={$appUserId}";
        }
        return $this->get($uri);
    }

    public function getProducts(): array
    {
        return $this->get('/products');
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
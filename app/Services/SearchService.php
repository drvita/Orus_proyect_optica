<?php

namespace App\Services;

use App\DTOs\Search\CreateNameRequest;
use App\DTOs\Search\SearchNameRequest;
use App\DTOs\Search\UpdateNameRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class SearchService
{
    private Client $client;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.app-search.base_url', 'http://search:8000');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
    }

    /**
     * Create a new name in the vector database
     *
     * @param CreateNameRequest $request
     * @return array
     * @throws RuntimeException
     */
    public function createName(CreateNameRequest $request): array
    {
        try {
            $response = $this->client->post('/fullname/create', [
                'json' => $request->toArray()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RuntimeException(
                "Error creating name in search service: " . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Search for similar names in the vector database
     *
     * @param SearchNameRequest $request
     * @return array
     * @throws RuntimeException
     */
    public function searchName(SearchNameRequest $request): array
    {
        try {
            $response = $this->client->post('/fullname/search', [
                'json' => array_merge(
                    $request->toArray(),
                    [
                        'limit' => $request->limit ?? config('services.app-search.default_limit'),
                        'min_similarity' => $request->min_similarity ?? config('services.app-search.default_similarity'),
                    ]
                )
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RuntimeException(
                "Error searching names in search service: " . $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * Update an existing name in the vector database
     *
     * @param UpdateNameRequest $request
     * @return array
     * @throws RuntimeException
     */
    public function updateName(UpdateNameRequest $request): array
    {
        try {
            $response = $this->client->post('/fullname/edit', [
                'json' => $request->toArray()
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new RuntimeException(
                "Error updating name in search service: " . $e->getMessage(),
                $e->getCode()
            );
        }
    }
}


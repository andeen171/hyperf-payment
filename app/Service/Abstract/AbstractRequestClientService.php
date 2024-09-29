<?php

namespace App\Service\Abstract;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Hyperf\Codec\Json;

abstract class AbstractRequestClientService
{
    private Client $client;
    protected string $baseUrl;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    /**
     * @throws ClientException|GuzzleException
     */
    protected function sendRequest(
        string $method,
        string $route,
        ?array $content = [],
        ?array $headers = [],
    ): array
    {
        $response = match ($method) {
            'POST' => $this->client->post($route, [
                RequestOptions::HEADERS => $headers,
                RequestOptions::JSON => $content,
            ]),
            'GET' => $this->client->get($route, [
                RequestOptions::HEADERS => $headers,
                RequestOptions::QUERY => $content,
            ]),
            'PUT' => $this->client->put($route, [
                RequestOptions::HEADERS => $headers,
                RequestOptions::JSON => $content,
            ]),
            'DELETE' => $this->client->delete($route, [
                RequestOptions::HEADERS => $headers,
                RequestOptions::JSON => $content,
            ]),
        };

        return Json::decode($response->getBody()->getContents());
    }
}
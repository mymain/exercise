<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class CachingHttpClient implements HttpClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly CacheInterface $cache,
        #[Autowire(env: 'HTTP_CACHE_TTL')]
        private readonly int $httpCacheTtl,
    ) {
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $requestHash = $this->getRequestHash($method, $url, $options);

        [$content, $statusCode, $headers] = $this->cache->get(
            $requestHash,
            function (ItemInterface $item) use ($method, $url, $options): array {
                $item->expiresAfter($this->httpCacheTtl);

                $response = $this->client->request($method, $url, $options);

                return [
                    $response->getContent(),
                    $response->getStatusCode(),
                    $response->getHeaders(),
                ];
            }
        );

        return MockResponse::fromRequest(
            $method,
            $url,
            $options,
            new MockResponse($content, [
                'http_code' => $statusCode,
                'response_headers' => $headers,
            ])
        );
    }

    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        $clone = clone $this;

        $clone->client = $this->client->withOptions($options);

        return $clone;
    }

    private function getRequestHash(string $method, string $url, array $options): string
    {
        return md5($method . $url . serialize($options));
    }
}

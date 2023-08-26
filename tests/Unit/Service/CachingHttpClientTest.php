<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\CachingHttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CachingHttpClientTest extends TestCase
{
    private const TTL = 300;
    private const URL = 'https://unit.pl';
    private const METHOD = Request::METHOD_GET;
    private const OPTIONS = [];
    private const RESPONSE = 'some response';
    private const RESPONSE_STATUS = Response::HTTP_OK;
    private const RESPONSE_HEADERS = [];

    private CachingHttpClient $object;

    private HttpClientInterface $httpClientMock;
    private CacheInterface $cacheMock;

    public function setUp(): void
    {
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->cacheMock = $this->createMock(CacheInterface::class);

        $this->object = new CachingHttpClient(
            $this->httpClientMock,
            $this->cacheMock,
            self::TTL,
        );
    }

    public function testRequest(): void
    {
        $this->cacheMock->expects($this->once())
            ->method('get')
            ->willReturn([
                self::RESPONSE,
                self::RESPONSE_STATUS,
                self::RESPONSE_HEADERS,
            ]);

        $response = $this->object->request(self::METHOD, self::URL, self::OPTIONS);

        $this->assertInstanceOf(
            ResponseInterface::class,
            $response
        );

        $this->assertEquals(
            self::RESPONSE,
            $response->getContent()
        );

        $this->assertEquals(
            self::RESPONSE_STATUS,
            $response->getStatusCode()
        );

        $this->assertEquals(
            self::RESPONSE_HEADERS,
            $response->getHeaders()
        );
    }
}

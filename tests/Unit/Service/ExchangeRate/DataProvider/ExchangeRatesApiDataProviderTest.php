<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\ExchangeRate;

use App\Service\ExchangeRate\DataProvider\ExchangeRatesApiDataProvider;
use App\Service\ExchangeRate\ExchangeRateConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ExchangeRatesApiDataProviderTest extends TestCase
{
    private const API_KEY = 'abc';
    private const API_URL = 'http://abc.pl';
    private const BASE_CURRENCY = 'PLN';

    private ExchangeRatesApiDataProvider $object;

    private MockHttpClient $mockHttpClient;
    private MockResponse $mockResponse;

    public function setUp(): void
    {
        $this->mockResponse = new MockResponse(json_encode(
            [
                'base' => 'PLN',
                'rates' => [
                    ['EUR' => 4.123112],
                ],
            ],
        ));

        $this->mockHttpClient = new MockHttpClient($this->mockResponse);

        $this->object = new ExchangeRatesApiDataProvider(
            httpClient: $this->mockHttpClient,
            exchangeRatesApiKey: self::API_KEY,
            exchangeRatesApiUrl: self::API_URL,
            baseCurrency: self::BASE_CURRENCY,
        );
    }

    public function testGetExchangeRateCalculator(): void
    {
        $this->assertInstanceOf(
            ExchangeRateConverter::class,
            $this->object->getExchangeRateConverter()
        );
    }

    public function testSupport(): void
    {
        $this->assertTrue($this->object->support(ExchangeRatesApiDataProvider::PROVIDER));
    }
}

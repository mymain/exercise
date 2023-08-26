<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\DataProvider;

use App\Service\CachingHttpClient;
use App\Service\ExchangeRate\ExchangeRateConverter;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;
use Money\Currency;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExchangeRatesApiDataProvider implements DataProviderInterface
{
    public const PROVIDER = 'EXCHANGE_RATES_API';

    public function __construct(
        #[Autowire(service: CachingHttpClient::class)]
        private readonly HttpClientInterface $httpClient,
        #[Autowire(env: 'EXCHANGE_RATES_API_KEY')]
        private readonly string $exchangeRatesApiKey,
        #[Autowire(env: 'EXCHANGE_RATES_API_URL')]
        private readonly string $exchangeRatesApiUrl,
        #[Autowire(env: 'BASE_CURRENCY')]
        private readonly string $baseCurrency,
    ) {
    }

    public function getExchangeRateConverter(): ExchangeRateConverterInterface
    {
        $response = $this->httpClient->request(
            Request::METHOD_GET,
            sprintf(
                '%s/v1/latest?access_key=%s&base=%s',
                $this->exchangeRatesApiUrl,
                $this->exchangeRatesApiKey,
                $this->baseCurrency,
            )
        )->toArray();

        return new ExchangeRateConverter(
            new Currency($response['base']),
            $response['rates'],
        );
    }

    public function support(string $dataSourceProvider): bool
    {
        return strtoupper($dataSourceProvider) === self::PROVIDER;
    }
}

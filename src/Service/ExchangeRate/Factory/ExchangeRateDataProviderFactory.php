<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Factory;

use App\Service\ExchangeRate\DataProvider\DataProviderInterface;
use App\Service\ExchangeRate\DataProvider\ExchangeRatesApiDataProvider;
use App\Service\ExchangeRate\Exception\ProviderNotFoundException;
use App\Service\ExchangeRate\ExchangeRateConverter;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ExchangeRateDataProviderFactory
{
    public function __construct(
        #[TaggedIterator('app.exchanger_rate_data_provider')]
        private iterable $providers,
    ) {
    }

    public function getDataProvider(
        string $dataProvider = ExchangeRatesApiDataProvider::PROVIDER
    ): ExchangeRateConverter {
        /** @var DataProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($provider->support($dataProvider)) {
                return $provider->getExchangeRateCalculator();
            }
        }

        throw new ProviderNotFoundException($dataProvider);
    }
}

<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Factory;

use App\Service\ExchangeRate\DataProvider\ExchangeRatesApiDataProvider;
use App\Service\ExchangeRate\ExchangeRateConverterInterface;

interface ExchangeRateDataProviderFactoryInterface
{
    public function getDataProvider(
        string $dataProvider = ExchangeRatesApiDataProvider::PROVIDER,
    ): ExchangeRateConverterInterface;
}

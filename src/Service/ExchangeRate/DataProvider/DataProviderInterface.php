<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\DataProvider;

use App\Service\ExchangeRate\ExchangeRateConverter;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.exchanger_rate_data_provider')]
interface DataProviderInterface
{
    public function getExchangeRateCalculator(): ExchangeRateConverter;
    public function support(string $dataSourceProvider): bool;
}

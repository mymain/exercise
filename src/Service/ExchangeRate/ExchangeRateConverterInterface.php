<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate;

use App\Service\ExchangeRate\Exception\RateNotFoundException;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use Money\Currency;
use Money\Money;

interface ExchangeRateConverterInterface
{
    /** @throws RateNotFoundException */
    public function convert(
        Money $baseAmount,
        Currency $baseCurrency,
        Currency $targetCurrency,
    ): ExchangeRateConversionResultInterface;
}

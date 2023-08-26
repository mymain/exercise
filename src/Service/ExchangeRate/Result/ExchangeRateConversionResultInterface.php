<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Result;

use Money\Currency;
use Money\Money;

interface ExchangeRateConversionResultInterface
{
    public function getBaseCurrency(): Currency;
    public function getTargetCurrency(): Currency;
    public function getBaseAmount(): Money;
    public function getTargetAmount(): Money;
    public function getExchangeRate(): float;
}

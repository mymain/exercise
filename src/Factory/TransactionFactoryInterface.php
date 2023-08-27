<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Transaction;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;

interface TransactionFactoryInterface
{
    public function fromExchangeConversionResult(
        ExchangeRateConversionResultInterface $exchangeRateConversionResult,
        string $ip,
    ): Transaction;
}

<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Transaction;
use App\Enum\PaymentMethod;
use App\Enum\TransactionType;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResult;

class TransactionFactory
{
    public function fromExchangeConversionResult(
        ExchangeRateConversionResult $exchangeRateConversionResult,
        string $ip,
    ): Transaction {
        $transaction = new Transaction();

        $transaction->baseCurrency = $exchangeRateConversionResult->baseCurrency->getCode();
        $transaction->targetCurrency = $exchangeRateConversionResult->targetCurrency->getCode();
        $transaction->baseAmount = (int) $exchangeRateConversionResult->baseAmount->getAmount();
        $transaction->targetAmount = (int) $exchangeRateConversionResult->targetAmount->getAmount();
        $transaction->paymentMethod = PaymentMethod::CARD->value;
        $transaction->transactionType = TransactionType::DEPOSIT->value;
        $transaction->exchangeRate = $exchangeRateConversionResult->exchangeRate;
        $transaction->ip = $ip;
        $transaction->transactionTimestamp = time();

        return $transaction;
    }
}

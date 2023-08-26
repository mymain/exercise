<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Transaction;
use App\Enum\PaymentMethod;
use App\Enum\TransactionType;
use App\Service\ExchangeRate\Result\ExchangeRateConversionResultInterface;
use Symfony\Component\Clock\ClockInterface;

final class TransactionFactory
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function fromExchangeConversionResult(
        ExchangeRateConversionResultInterface $exchangeRateConversionResult,
        string $ip,
    ): Transaction {
        return (new Transaction())
            ->setBaseCurrency($exchangeRateConversionResult->getBaseCurrency()->getCode())
            ->setTargetCurrency($exchangeRateConversionResult->getTargetCurrency()->getCode())
            ->setBaseAmount((int) $exchangeRateConversionResult->getBaseAmount()->getAmount())
            ->setTargetAmount((int) $exchangeRateConversionResult->getTargetAmount()->getAmount())
            ->setPaymentMethod(PaymentMethod::CARD->value)
            ->setTransactionType(TransactionType::DEPOSIT->value)
            ->setExchangeRate($exchangeRateConversionResult->getExchangeRate())
            ->setIp($ip)
            ->setTransactionTimestamp($this->clock->now()->getTimestamp());
    }
}

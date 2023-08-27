<?php

declare(strict_types=1);

namespace App\Messenger\CommandHandler;

use App\Factory\TransactionFactoryInterface;
use App\Messenger\Command\TransactionExchangeCommand;
use App\Entity\Transaction;
use App\Repository\TransactionRepositoryInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactoryInterface;
use Money\Currency;
use Money\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;

#[AsMessageHandler]
final class TransactionExchangeCommandHandler
{
    use HandleTrait;

    public function __construct(
        private readonly ExchangeRateDataProviderFactoryInterface $exchangeRateDataProviderFactory,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionFactoryInterface $transactionFactory,
    ) {
    }

    public function __invoke(TransactionExchangeCommand $command): Transaction
    {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider();
        $transactionExchangeDto = $command->transactionExchangeDto;

        $exchangeConversionResult = $provider->convert(
            baseAmount: new Money(
                $transactionExchangeDto->baseAmount,
                new Currency($transactionExchangeDto->baseCurrency)
            ),
            baseCurrency: new Currency($transactionExchangeDto->baseCurrency),
            targetCurrency: new Currency($transactionExchangeDto->targetCurrency),
        );

        $transaction = $this->transactionFactory->fromExchangeConversionResult(
            exchangeRateConversionResult: $exchangeConversionResult,
            ip: $command->ip,
        );

        $this->transactionRepository->persist($transaction);

        return $transaction;
    }
}

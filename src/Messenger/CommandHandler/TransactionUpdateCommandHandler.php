<?php

declare(strict_types=1);

namespace App\Messenger\CommandHandler;

use App\Messenger\Command\TransactionUpdateCommand;
use App\Entity\Transaction;
use App\Factory\TransactionFactory;
use App\Repository\TransactionRepository;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use Money\Currency;
use Money\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;

#[AsMessageHandler]
final class TransactionUpdateCommandHandler
{
    use HandleTrait;

    public function __construct(
        private readonly ExchangeRateDataProviderFactory $exchangeRateDataProviderFactory,
        private readonly TransactionRepository $transactionRepository,
        private readonly TransactionFactory $transactionFactory,
    ) {
    }

    public function __invoke(TransactionUpdateCommand $command): Transaction
    {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider();
        $transaction = $this->transactionRepository->findById($command->transactionUpdateDto->transactionId);
        $transactionUpdateDto = $command->transactionUpdateDto;

        $exchangeConversionResult = $provider->convert(
            baseAmount: new Money($transaction->baseAmount, new Currency($transaction->baseCurrency)),
            baseCurrency: new Currency($transaction->baseCurrency),
            targetCurrency: new Currency($transactionUpdateDto->targetCurrency),
        );

        $transaction->targetCurrency = $exchangeConversionResult->targetCurrency->getCode();
        $transaction->targetAmount = (int) $exchangeConversionResult->targetAmount->getAmount();
        $transaction->exchangeRate = $exchangeConversionResult->exchangeRate;

        $this->transactionRepository->flush();

        return $transaction;
    }
}

<?php

declare(strict_types=1);

namespace App\Messenger\CommandHandler;

use App\Factory\TransactionFactoryInterface;
use App\Messenger\Command\TransactionUpdateCommand;
use App\Entity\Transaction;
use App\Repository\TransactionRepositoryInterface;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactoryInterface;
use Money\Currency;
use Money\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;

#[AsMessageHandler]
final class TransactionUpdateCommandHandler
{
    use HandleTrait;

    public function __construct(
        private readonly ExchangeRateDataProviderFactoryInterface $exchangeRateDataProviderFactory,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionFactoryInterface $transactionFactory,
    ) {
    }

    public function __invoke(TransactionUpdateCommand $command): Transaction
    {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider();
        $transaction = $this->transactionRepository->findById($command->transactionUpdateDto->transactionId);
        $transactionUpdateDto = $command->transactionUpdateDto;

        $exchangeConversionResult = $provider->convert(
            baseAmount: new Money($transaction->getBaseAmount(), new Currency($transaction->getBaseCurrency())),
            baseCurrency: new Currency($transaction->getBaseCurrency()),
            targetCurrency: new Currency($transactionUpdateDto->targetCurrency),
        );

        $transaction->setTargetCurrency($exchangeConversionResult->getTargetCurrency()->getCode())
            ->setTargetAmount((int) $exchangeConversionResult->getTargetAmount()->getAmount())
            ->setExchangeRate($exchangeConversionResult->getExchangeRate());

        $this->transactionRepository->flush();

        return $transaction;
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Service\ExchangeRate\DataProvider\ExchangeRatesApiDataProvider;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use Money\Currency;
use Money\Money;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    public function __construct(
        private readonly ExchangeRateDataProviderFactory $exchangeRateDataProviderFactory,
        #[Autowire(env: 'BASE_CURRENCY')]
        private readonly string $baseCurrency,
        private readonly TransactionRepository $transactionRepository,
    ) {
    }

    #[Route('/test')]
    public function test(): Response
    {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider(
            ExchangeRatesApiDataProvider::PROVIDER
        );

        $baseCurrency = new Currency($this->baseCurrency);
        $targetCurrency = new Currency('EUR');
        $baseAmount = 100;
        $amount = new Money($baseAmount, $baseCurrency);
        $converted = $provider->convert(
            $amount,
            $baseCurrency,
            $targetCurrency,
        );

        $transaction = new Transaction();
        $transaction->baseCurrency = $baseCurrency->getCode();
        $transaction->targetCurrency = $targetCurrency->getCode();
        $transaction->baseAmount = $baseAmount;
        $transaction->targetAmount = (int) $converted->getAmount();
        $transaction->paymentMethod = 'card';
        $transaction->transactionType = 'deposit';
        $transaction->exchangeRate = 'tbd';
        $transaction->ip = 'tbd';
        $transaction->transactionTimestamp = 1;

        $this->transactionRepository->save($transaction);

        dd('done', $converted);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Factory\TransactionFactory;
use App\Repository\TransactionRepository;
use App\Service\ExchangeRate\DataProvider\ExchangeRatesApiDataProvider;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use Money\Currency;
use Money\Money;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    public function __construct(
        private readonly ExchangeRateDataProviderFactory $exchangeRateDataProviderFactory,
        #[Autowire(env: 'BASE_CURRENCY')]
        private readonly string $baseCurrency,
        private readonly TransactionRepository $transactionRepository,
        private readonly TransactionFactory $transactionFactory,
    ) {
    }

    #[Route('/test')]
    public function test(Request $request): Response
    {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider();

        $exchangeConversionResult = $provider->convert(
            baseAmount:  new Money(100000000, new Currency($this->baseCurrency)),
            baseCurrency: new Currency($this->baseCurrency),
            targetCurrency: new Currency('BTC'),
        );

        $this->transactionRepository->save(
            $this->transactionFactory->fromExchangeConversionResult(
                exchangeRateConversionResult: $exchangeConversionResult,
                ip: $request->getClientIp(),
            ),
        );

        dd('done', $exchangeConversionResult);
    }
}

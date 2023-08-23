<?php

declare(strict_types=1);

namespace App\Controller;

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
    ) {
    }

    #[Route('/test')]
    public function test(): Response
    {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider(
            ExchangeRatesApiDataProvider::PROVIDER
        );

        $converted = $provider->convert(
            new Money('100', new Currency($this->baseCurrency)),
            new Currency('EUR'),
            new Currency('EUR'),
        );

        dd($converted, 'done');
    }
}

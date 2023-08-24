<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ExchangeMoneyDto;
use App\Factory\TransactionFactory;
use App\Repository\TransactionRepository;
use App\Service\ExchangeRate\DataProvider\ExchangeRatesApiDataProvider;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'BASE_CURRENCY')]
        private readonly string $baseCurrency,
        private readonly ExchangeRateDataProviderFactory $exchangeRateDataProviderFactory,
        private readonly TransactionRepository $transactionRepository,
        private readonly TransactionFactory $transactionFactory,
    ) {
    }

    #[Route('/')]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'currencies' => new ISOCurrencies(),
        ]);
    }

    #[Route('transaction/exchange')]
    public function exchange(
        Request $request,
        #[MapRequestPayload]
        ExchangeMoneyDto $exchangeMoneyDto,
    ): Response {
        $provider = $this->exchangeRateDataProviderFactory->getDataProvider();

        $exchangeConversionResult = $provider->convert(
            baseAmount:  new Money($exchangeMoneyDto->baseAmount, new Currency($exchangeMoneyDto->baseCurrency)),
            baseCurrency: new Currency($this->baseCurrency),
            targetCurrency: new Currency($exchangeMoneyDto->targetCurrency),
        );

        $transaction = $this->transactionFactory->fromExchangeConversionResult(
            exchangeRateConversionResult: $exchangeConversionResult,
            ip: $request->getClientIp(),
        );

        $this->transactionRepository->save($transaction);

        return $this->json([
            'id' => $transaction->id,
        ]);
    }
}

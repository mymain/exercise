<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ExchangeMoneyDto;
use App\Factory\TransactionFactory;
use App\Repository\TransactionRepository;
use App\Service\ExchangeRate\Exception\ProviderNotFoundException;
use App\Service\ExchangeRate\Exception\RateNotFoundException;
use App\Service\ExchangeRate\Factory\ExchangeRateDataProviderFactory;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('exchange', methods: Request::METHOD_POST)]
final class ExchangeController extends AbstractController
{
    public function __construct(
        private readonly ExchangeRateDataProviderFactory $exchangeRateDataProviderFactory,
        private readonly TransactionRepository $transactionRepository,
        private readonly TransactionFactory $transactionFactory,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapRequestPayload]
        ExchangeMoneyDto $exchangeMoneyDto,
    ): Response {
        try {
            $provider = $this->exchangeRateDataProviderFactory->getDataProvider();

            $exchangeConversionResult = $provider->convert(
                baseAmount: new Money($exchangeMoneyDto->baseAmount, new Currency($exchangeMoneyDto->baseCurrency)),
                baseCurrency: new Currency($exchangeMoneyDto->baseCurrency),
                targetCurrency: new Currency($exchangeMoneyDto->targetCurrency),
            );
        } catch (RateNotFoundException | ProviderNotFoundException) {
            return $this->json('error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $transaction = $this->transactionFactory->fromExchangeConversionResult(
            exchangeRateConversionResult: $exchangeConversionResult,
            ip: $request->getClientIp(),
        );

        $this->transactionRepository->persist($transaction);

        return $this->json($transaction);
    }
}

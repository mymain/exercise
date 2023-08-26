<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TransactionUpdateDto;
use App\Entity\Transaction;
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

#[Route('/transaction-update/{id}', methods:[Request::METHOD_POST])]
final class TransactionUpdateController extends AbstractController
{
    public function __construct(
        private readonly ExchangeRateDataProviderFactory $exchangeRateDataProviderFactory,
        private readonly TransactionRepository $transactionRepository,
    ) {
    }

    public function __invoke(
        Transaction $transaction,
        #[MapRequestPayload]
        TransactionUpdateDto $transactionUpdateDto,
    ): Response {
        try {
            $provider = $this->exchangeRateDataProviderFactory->getDataProvider();

            $exchangeConversionResult = $provider->convert(
                baseAmount: new Money($transaction->baseAmount, new Currency($transaction->baseCurrency)),
                baseCurrency: new Currency($transaction->baseCurrency),
                targetCurrency: new Currency($transactionUpdateDto->targetCurrency),
            );

            $transaction->targetCurrency = $exchangeConversionResult->targetCurrency->getCode();
            $transaction->targetAmount = (int) $exchangeConversionResult->targetAmount->getAmount();
            $transaction->exchangeRate = $exchangeConversionResult->exchangeRate;

            $this->transactionRepository->flush();
        } catch (RateNotFoundException | ProviderNotFoundException) {
            return $this->json('error', Response::HTTP_FAILED_DEPENDENCY);
        }

        return $this->json($transaction);
    }
}

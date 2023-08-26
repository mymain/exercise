<?php

declare(strict_types=1);

namespace App\Controller;

use App\Messenger\Command\TransactionExchangeCommand;
use App\Dto\TransactionExchangeDto;
use App\Entity\Transaction;
use App\Trait\GetEnvelopeResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('exchange', methods: Request::METHOD_POST)]
final class TransactionExchangeController extends AbstractController
{
    use GetEnvelopeResult;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapRequestPayload]
        TransactionExchangeDto $exchangeDto,
    ): Response {
        try {
            $envelope = $this->commandBus->dispatch(new TransactionExchangeCommand(
                exchangeDto: $exchangeDto,
                ip: $request->getClientIp(),
            ));

            /** @var Transaction $transaction */
            $transaction = $this->getEnvelopeResult($envelope);
        } catch (HandlerFailedException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($transaction);
    }
}

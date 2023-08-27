<?php

declare(strict_types=1);

namespace App\Controller;

use App\Messenger\Command\TransactionUpdateCommand;
use App\Dto\TransactionUpdateDto;
use App\Entity\Transaction;
use App\Trait\GetEnvelopeResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction-update', methods:[Request::METHOD_POST])]
final class TransactionUpdateController extends AbstractController
{
    use GetEnvelopeResult;

    public function __construct(
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload]
        TransactionUpdateDto $transactionUpdateDto,
    ): Response {
        try {
            $envelope = $this->commandBus->dispatch(new TransactionUpdateCommand(
                transactionUpdateDto: $transactionUpdateDto,
            ));

            /** @var Transaction $transaction */
            $transaction = $this->getEnvelopeResult($envelope);
        } catch (HandlerFailedException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($transaction);
    }
}

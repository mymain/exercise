<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\PaginationDto;
use App\Repository\TransactionRepository;
use App\Service\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction-list', methods:[Request::METHOD_GET])]
final class TransactionListController extends AbstractController
{
    public function __construct(
        private readonly Paginator $paginator,
        private readonly TransactionRepository $transactionRepository,
    ) {
    }

    public function __invoke(
        #[MapQueryString]
        PaginationDto $paginationDto = new PaginationDto(),
    ): Response {
        return $this->render('list.html.twig', [
            'paginator' => $this->paginator->paginate(
                $this->transactionRepository->getTransactionsQueryBuilder(),
                $paginationDto,
            ),
        ]);
    }
}

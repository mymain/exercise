<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Transaction;
use Money\Currencies\ISOCurrencies;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction-edit/{id}', methods:[Request::METHOD_GET])]
final class TransactionEditController extends AbstractController
{
    public function __invoke(Transaction $transaction): Response
    {
        return $this->render('edit.html.twig', [
            'currencies' => new ISOCurrencies(),
            'transaction' => $transaction,
        ]);
    }
}

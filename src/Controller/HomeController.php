<?php

declare(strict_types=1);

namespace App\Controller;

use Money\Currencies\ISOCurrencies;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', methods:[Request::METHOD_GET])]
final class HomeController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('index.html.twig', [
            'currencies' => new ISOCurrencies(),
        ]);
    }
}

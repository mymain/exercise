<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 32)]
    public string $paymentMethod;

    #[ORM\Column(length: 32)]
    public string $transactionType;

    #[ORM\Column(type: Types::INTEGER)]
    public int $transactionTimestamp;

    #[ORM\Column(type: Types::INTEGER)]
    public int $baseAmount;

    #[ORM\Column(length: 3)]
    public string $baseCurrency;

    #[ORM\Column(type: Types::INTEGER)]
    public int $targetAmount;

    #[ORM\Column(length: 3)]
    public string $targetCurrency;

    #[ORM\Column(type: Types::FLOAT)]
    public float $exchangeRate;

    #[ORM\Column(length: 128)]
    public string $ip;
}

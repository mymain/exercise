<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TransactionRepository;
use App\Validator\MoneyCurrency;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 32)]
    #[Assert\Length(max: 32)]
    #[Assert\NotBlank]
    private string $paymentMethod;

    #[ORM\Column(length: 32)]
    #[Assert\Length(max: 32)]
    #[Assert\NotBlank]
    private string $transactionType;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    private int $transactionTimestamp;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    private int $baseAmount;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank]
    #[Assert\Length(exactly: 3)]
    #[MoneyCurrency]
    private string $baseCurrency;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    private int $targetAmount;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank]
    #[Assert\Length(exactly: 3)]
    #[MoneyCurrency]
    private string $targetCurrency;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank]
    private float $exchangeRate;

    #[ORM\Column(length: 128)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private string $ip;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function getTransactionTimestamp(): int
    {
        return $this->transactionTimestamp;
    }

    public function setTransactionTimestamp(int $transactionTimestamp): self
    {
        $this->transactionTimestamp = $transactionTimestamp;

        return $this;
    }

    public function getBaseAmount(): int
    {
        return $this->baseAmount;
    }

    public function setBaseAmount(int $baseAmount): self
    {
        $this->baseAmount = $baseAmount;

        return $this;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): self
    {
        $this->baseCurrency = $baseCurrency;

        return $this;
    }

    public function getTargetAmount(): int
    {
        return $this->targetAmount;
    }

    public function setTargetAmount(int $targetAmount): self
    {
        $this->targetAmount = $targetAmount;

        return $this;
    }

    public function getTargetCurrency(): string
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(string $targetCurrency): self
    {
        $this->targetCurrency = $targetCurrency;

        return $this;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(float $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }
}

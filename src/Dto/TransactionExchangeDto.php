<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class TransactionExchangeDto
{
    public function __construct(
        #[Assert\Currency]
        #[Assert\NotBlank]
        public readonly string $baseCurrency,
        #[Assert\GreaterThan(0)]
        #[Assert\NotBlank]
        public readonly int $baseAmount,
        #[Assert\Currency]
        #[Assert\NotBlank]
        public readonly string $targetCurrency,
    ) {
    }
}
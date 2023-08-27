<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\MoneyCurrency;
use Symfony\Component\Validator\Constraints as Assert;

final class TransactionExchangeDto
{
    public function __construct(
        #[MoneyCurrency]
        #[Assert\NotBlank]
        public readonly string $baseCurrency,
        #[Assert\GreaterThan(0)]
        #[Assert\NotBlank]
        public readonly int $baseAmount,
        #[MoneyCurrency]
        #[Assert\NotBlank]
        public readonly string $targetCurrency,
    ) {
    }
}

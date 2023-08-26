<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class TransactionUpdateDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $transactionId,
        #[Assert\Currency]
        #[Assert\NotBlank]
        public readonly string $targetCurrency,
    ) {
    }
}

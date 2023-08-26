<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PaginationDto
{
    public function __construct(
        #[Assert\Positive]
        #[Assert\GreaterThan(0)]
        public readonly int $page = 1,
    ) {
    }
}

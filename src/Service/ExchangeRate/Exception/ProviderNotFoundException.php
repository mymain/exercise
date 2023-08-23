<?php

declare(strict_types=1);

namespace App\Service\ExchangeRate\Exception;

use Exception;
use Throwable;

final class ProviderNotFoundException extends Exception
{
    public function __construct(string $provider, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(sprintf('Conversion rate data provider %s not found', $provider), $code, $previous);
    }
}

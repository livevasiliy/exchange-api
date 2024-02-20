<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ExchangeServiceContract;

class ExchangeService
{
    public function __construct(private readonly ExchangeServiceContract $exchangeServiceContract)
    {
    }

    public function rates(string $currency): array
    {
        return $this->exchangeServiceContract->getRate($currency);
    }

    public function exchange(string $currencyFrom, string $currencyTo, float $value): array
    {
        return $this->exchangeServiceContract->convert($currencyFrom, $currencyTo, $value);
    }
}

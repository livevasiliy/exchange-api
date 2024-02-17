<?php declare(strict_types=1);

namespace App\Contracts;

interface ExchangeServiceContract
{
    public function getRates(): array;

    public function getRate(string $currency): array;

    public function convert(string $currencyFrom, string $currencyTo, float $value): array;
}

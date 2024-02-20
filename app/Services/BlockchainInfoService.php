<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ExchangeServiceContract;
use App\Exceptions\FailFetchListOfRatesException;
use App\Exceptions\InvalidCurrencyForExchangeException;
use App\Exceptions\NotFoundCurrencyException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class BlockchainInfoService implements ExchangeServiceContract
{
    public const COMMISSION = 0.02;

    private const MIN_EXCHANGE_AMOUNT = 0.01;

    private const MAX_PRECISION_VALUE = 10;

    public function __construct(private readonly Http $httpClient)
    {
    }

    public function getRates(): array
    {
        $request = $this->httpClient::get(
            sprintf('%s/%s',
                config('services.blockchainInfo.base_uri'),
                config('services.blockchainInfo.endpoint')
            )
        );

        if ($request->status() !== Response::HTTP_OK) {
            throw new FailFetchListOfRatesException();
        }

        return $request->json();
    }

    public function getRate(string $currency): array
    {
        $rates = $this->getRates();

        if (empty($rates)) {
            throw new FailFetchListOfRatesException();
        }

        if (! in_array($currency, array_keys($rates), true)) {
            throw new NotFoundCurrencyException($currency);
        }

        $result = [];

        foreach ($rates[$currency] as $key => $value) {
            if (is_numeric($value)) {
                $result[$key] = $this->addCommission($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function addCommission(int|float $value): int|float
    {
        return $value + ($value * self::COMMISSION);
    }

    public function convert(string $currencyFrom, string $currencyTo, float $value): array
    {
        $currencies = $this->getRates();

        if (! isset($currencies[$currencyFrom]) || ! isset($currencies[$currencyTo])) {
            throw new InvalidCurrencyForExchangeException($currencyFrom, $currencyTo);
        }

        $exchangeRateFrom = $currencies[$currencyFrom]['sell'];
        $exchangeRateTo = $currencies[$currencyTo]['buy'];

        $convertedAmount = ($value / $exchangeRateFrom) * $exchangeRateTo;

        $convertedAmount = max(self::MIN_EXCHANGE_AMOUNT, round($convertedAmount, self::MAX_PRECISION_VALUE));

        return [
            'currency_from' => $currencyFrom,
            'currency_to' => $currencyTo,
            'value' => $value,
            'converted_value' => $convertedAmount,
            'rate_sell' => $exchangeRateFrom,
            'rate_buy' => $exchangeRateTo,
            'commission_value' => $this->addCommission($convertedAmount),
        ];
    }
}

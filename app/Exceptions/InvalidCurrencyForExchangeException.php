<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class InvalidCurrencyForExchangeException extends Exception
{
    public function __construct(string $currencyFrom, string $currencyTo, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(trans('errors.invalid.currency.for.exchange', [
            'from' => $currencyFrom,
            'to' => $currencyTo,
        ]), $code, $previous);
    }

}

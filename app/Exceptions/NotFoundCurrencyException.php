<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFoundCurrencyException extends Exception
{
    public function __construct(string $currency, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(trans('errors.not.found.currency.for.rate', [
            'currency' => $currency,
        ]), $code, $previous);
    }

}

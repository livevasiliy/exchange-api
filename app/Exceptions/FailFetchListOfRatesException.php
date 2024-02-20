<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class FailFetchListOfRatesException extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(trans('errors.fail.fetch.list.of.rates'), $code, $previous);
    }

}

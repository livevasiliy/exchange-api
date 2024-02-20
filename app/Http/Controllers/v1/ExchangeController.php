<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Constants\AvailableMethodsConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ExchangeRequest;
use App\Services\ExchangeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ExchangeController extends Controller
{
    public function __invoke(ExchangeRequest $request, ExchangeService $exchangeService): JsonResponse
    {
        try {
            $data = match ($request->validated('method')) {
                AvailableMethodsConstants::RATES => $exchangeService->rates($request->validated('currency')),
                AvailableMethodsConstants::CONVERT => $exchangeService->exchange(
                    $request->validated('currency_from'),
                    $request->validated('currency_to'),
                    (float) $request->validated('value')
                )
            };

            return new JsonResponse($data);
        } catch (Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            return new JsonResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

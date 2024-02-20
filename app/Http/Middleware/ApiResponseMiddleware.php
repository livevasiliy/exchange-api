<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Constants\StatusConstants;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $statusCode = $response->getStatusCode();

            $responseData = [
                'status' => $statusCode >= Response::HTTP_BAD_REQUEST ? StatusConstants::ERROR : StatusConstants::SUCCESS,
                'code' => $statusCode,
                'data' => $response->getOriginalContent(),
            ];

            $response->setContent(json_encode($responseData));
        }

        return $response;
    }
}

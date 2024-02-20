<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSupportedMethodMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('method') === false) {
            return new JsonResponse(trans('errors.missing.method.param'), Response::HTTP_BAD_REQUEST);
        }

        $method = $request->get('method');
        if (empty(trim($method))) {
            return new JsonResponse(trans('errors.invalid.http.method'), Response::HTTP_BAD_REQUEST);
        }

        if (! in_array($method, array_keys($this->getSupportedMethods()))) {
            return new JsonResponse(trans('errors.invalid.http.method'), Response::HTTP_BAD_REQUEST);
        }

        if ($request->method() !== $this->getSupportedMethods()[$method]) {
            return new JsonResponse(trans('errors.not.allow.http.method'), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        return $next($request);
    }

    /**
     * @return array<string, string>
     */
    private function getSupportedMethods(): array
    {
        return [
            'rates' => Request::METHOD_GET,
            'convert' => Request::METHOD_POST,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthAPIKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->headers->has('Authorization') === false) {
            return new JsonResponse(trans('errors.missing.api.token'), Response::HTTP_UNAUTHORIZED);
        }

        $apiToken = Str::after($request->headers->get('Authorization'), 'Bearer ');

        if ($apiToken === null) {
            return new JsonResponse(trans('errors.invalid.api.token'), Response::HTTP_UNAUTHORIZED);
        }

        if ($apiToken !== config('api.token.value') || strlen($apiToken) !== (int) config('api.token.length')) {
            return new JsonResponse(trans('errors.invalid.api.token'), Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}

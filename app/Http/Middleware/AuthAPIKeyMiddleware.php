<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Constants\StatusConstants;

class AuthAPIKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (false === $request->headers->has('Authorization')) {
            return new JsonResponse([
                'status' => StatusConstants::ERROR,
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => trans('errors.missing.api.token'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $apiToken = Str::after($request->headers->get('Authorization'), 'Bearer ');

        if (null === $apiToken) {
            return new JsonResponse([
                'status' => StatusConstants::ERROR,
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => trans('errors.invalid.api.token'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($apiToken !== config('api.token.value') || strlen($apiToken) !== (int)config('api.token.length')) {
            return new JsonResponse([
                'status' => StatusConstants::ERROR,
                'code' => Response::HTTP_UNAUTHORIZED,
                'message' => trans('errors.invalid.api.token'),
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}

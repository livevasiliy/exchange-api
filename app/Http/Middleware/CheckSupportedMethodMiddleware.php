<?php declare(strict_types=1);

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
     * @param Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (false === $request->has('method')) {
            return new JsonResponse([
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Missing required parameter `method`',
            ]);
        }

        $method = $request->get('method');
        if (empty(trim($method))) {
            return new JsonResponse([
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Invalid method provided',
            ]);
        }

        if (! in_array($method, array_keys($this->getSupportedMethods()))) {
            return new JsonResponse([
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Invalid method provided',
            ]);
        }

        if ($request->method() !== $this->getSupportedMethods()[$method]) {
            return new JsonResponse([
                'status' => 'error',
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'No allow HTTP method',
            ]);
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

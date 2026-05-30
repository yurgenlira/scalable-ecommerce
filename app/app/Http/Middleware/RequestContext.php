<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id') ?? (string) Str::uuid();
        $start = microtime(true);

        $response = $next($request);

        Log::withContext(['request_id' => $requestId, 'user_id' => optional($request->user())->id]);
        Log::info('request', [
            'method' => $request->method(),
            'path' => '/'.ltrim($request->path(), '/'),
            'status' => $response->getStatusCode(),
            'duration_ms' => round((microtime(true) - $start) * 1000, 1),
        ]);

        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}

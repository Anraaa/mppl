<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $expectedKey = env('API_KEY'); // or hardcode: 'your-secret-key'
        $providedKey = $request->header('X-API-KEY');

        if ($providedKey !== $expectedKey) {
            return response()->json([
                'message' => 'Unauthorized: Invalid API Key'
            ], 401);
        }

        return $next($request);
    }
}
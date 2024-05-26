<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $allowedOrigins = config('cors.allowed_origins', []);
        $allowedMethods = config('cors.allowed_methods', ['*']);
        $allowedHeaders = config('cors.allowed_headers', ['*']);
        $supportsCredentials = config('cors.supports_credentials', false) ? 'true' : 'false';

        $origin = $request->header('Origin');

        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
            $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
            $response->headers->set('Access-Control-Allow-Credentials', $supportsCredentials);

            if ($request->isMethod('OPTIONS')) {
                $response->setStatusCode(200);
                return $response;
            }
        }

        return $response;
    }
}

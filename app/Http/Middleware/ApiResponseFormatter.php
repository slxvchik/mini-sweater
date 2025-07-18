<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiResponseFormatter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            
            $response = $next($request);

            if (!$response instanceof Response || !$response->isSuccessful()) {
                return $response;
            }

            return response()->json([
                'status' => 'success',
                'code' => $response->getStatusCode(),
                'data' => $response->getContent(),
                'message' => 'OK',
                'errors' => null
            ], $response->getStatusCode());

        } catch (Throwable $ex) {
            throw $ex;
        }

    }

}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middleware\ApiResponseFormatter::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $ex, Request $request) {
            
            $statusCode = determineStatusCode($ex);

            $errors = $ex->getMessage();
            if ($ex instanceof ValidationException) { 
                $statusCode = $ex->status;
                $errors =  $ex->errors(); 
            }

            return response()->json([
                'status' =>  'error',
                'code' => $statusCode,
                'data' => null,
                'message' => $ex->getMessage(),
                'errors' => $errors
            ], $statusCode);

        });

        function determineStatusCode(Throwable $ex): int {
            if (method_exists($ex, 'getStatusCode')) {
                return $ex->getStatusCode();
            }
            
            if ($ex instanceof ValidationException) {
                return $ex->status;
            }
            
            $code = $ex->getCode();
            return ($code > 0 && $code < 600) ? $code : 500;
        }

    })->create();

    
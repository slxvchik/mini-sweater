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
        
        /** Затем вы должны указать Laravel, что входящие запросы от вашего SPA
         * могут аутентифицироваться с использованием файлов cookie сеанса Laravel,
         * при этом позволяя запросам третьих сторон или мобильных приложений аутентифицироваться
         * с использованием токенов API. */
        $middleware->statefulApi();

        // $middleware->api(prepend: [
        //     \App\Http\Middleware\ApiResponseFormatter::class
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $ex, Request $request) {
            
            $statusCode = determineStatusCode($ex);

            $response = [
                'success' => false,
                'message' => $ex->getMessage()
            ];

            if ($ex instanceof ValidationException) { 
                $statusCode = $ex->status;
                $response['data'] =  $ex->errors(); 
            }

            return response()->json($response, $statusCode);

        });

        function determineStatusCode(Throwable $ex): int {
            
            if ($ex instanceof ValidationException) {
                return $ex->status;
            }

            $exceptionCode = $ex->getCode();
    
            if (is_numeric($exceptionCode) && $exceptionCode >= 100 && $exceptionCode < 600) {
                return (int)$exceptionCode;
            }
            
            if ($ex instanceof \PDOException || $ex instanceof \Illuminate\Database\QueryException) {
                return 500;
            }

            return 500;
        }

    })->create();

    
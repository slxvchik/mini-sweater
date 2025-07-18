<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function (){

    Route::prefix('users')->controller(UserController::class)->group(function (){
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/', 'update');
        Route::delete('/', 'delete');
        Route::get('/{username}', 'get');
        Route::post('/change_email', 'changeEmail');
        Route::post('/change_username', 'changeUsername');
        Route::post('/change_password', 'changePassword');
    });
    
    Route::prefix('tweet')->controller()->group(function() {
        
    });

    // Route::get('/search?users={username}', [SearchController::class, 'findUsersByUsername']);
});
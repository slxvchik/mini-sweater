<?php

use App\Http\Controllers\TweetController;
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

        Route::get('/{username}', 'show');
        Route::put('/{username}', 'update');
        Route::patch('/{username}', 'partitialUpdate');
        Route::delete('/{username}', 'destroy');

        Route::get('/{username}/latest_tweets', [TweetController::class, 'getUserLatestTweets']);
        
        Route::prefix('{id}/account')->group(function (){
            Route::patch('/email', 'changeEmail');
            Route::patch('/username', 'changeUsername');
            Route::patch('/password', 'changePassword');
        });
    });
    
    Route::prefix('tweets')->controller(TweetController::class)->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        
        Route::put('/{tweet_id}', 'update');
        Route::delete('/{tweet_id}', 'destroy');
    });

    // Route::get('/search?users={username}', [SearchController::class, 'findUsersByUsername']);
});
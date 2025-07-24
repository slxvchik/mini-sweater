<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (){

    // todo: Auth routes
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    })->middleware('throttle:5,1');

    Route::prefix('users')->controller(UserController::class)->group(function (){
        
        Route::get('/', 'index');
        Route::get('/{username}', 'show');
        
        //todo:
        // Follow system
        // Route::get('/{username}/followers', 'followers');
        // Route::get('/{username}/following', 'following');
        // Route::post('/{username}/follow', 'follow')->middleware('auth:sanctum');
        // Route::delete('/{username}/unfollow', 'unfollow')->middleware('auth:sanctum');
        
        // Change authenticated user info
    });

    Route::prefix('/account')->controller(AccountController::class)->middleware('auth:sanctum')->group(function (){
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::patch('/', 'partitialUpdate');
        Route::delete('/', 'destroy');
        
        Route::patch('/email', 'changeEmail');
        Route::patch('/username', 'changeUsername');
        Route::patch('/password', 'changePassword');
    });
    
    Route::prefix('tweets')->controller(TweetController::class)->group(function() {
        
        Route::get('/', 'index');
        Route::post('/', 'store')->middleware('auth:sanctum');
        Route::get('/{tweet_id}', 'show');
        Route::put('/{tweet_id}', 'update')->middleware('auth:sanctum');
        Route::delete('/{tweet_id}', 'destroy')->middleware('auth:sanctum');

        //Feed
        // Route::get('/feed', 'feed')->middleware('auth:sanctum');
    });

    Route::prefix('comments')->controller(CommentController::class)->group(function() {
        
        Route::get('/', 'index');
        Route::post('/', 'store')->middleware('auth:sanctum');
        Route::get('/{comment_id}', 'show');
        Route::put('/{comment_id}', 'update')->middleware('auth:sanctum');
        Route::delete('/{comment_id}', 'destroy')->middleware('auth:sanctum');

        Route::get('/tweet/{tweet_id}', 'tweetComments');
    });

    Route::prefix('likes')->controller(LikeController::class)->group(function() {
        Route::get('/', 'index');

        Route::post('/tweet', 'storeLikeTweet')->middleware('auth:sanctum');
        Route::post('/comment', 'storeLikeComment')->middleware('auth:sanctum');


        Route::get('/{like_id}', 'show');
        Route::delete('/{like_id}', 'destroy')->middleware('auth:sanctum');

        // todo: Tweet likes
        // Route::get('/tweet/{tweet_id}', 'tweetLikes');
        // Route::get('/tweet/{tweet_id}/check', 'checkTweetLike')->middleware('auth:sanctum');

        // todo: Comment likes
        // Route::get('/comment/{comment_id}', 'commentLikes');
        // Route::get('/comment/{comment_id}/check', 'checkCommentLike')->middleware('auth:sanctum');
    });

    //todo
    // Search routes (future)
    // Route::prefix('search')->controller(SearchController::class)->group(function () {
    //     Route::get('/users', 'searchUsers');
    //     Route::get('/tweets', 'searchTweets');
    // });
    // Route::get('/search?users={username}', [SearchController::class, 'findUsersByUsername']);
    // Route::get('/{username}/latest_tweets', [TweetController::class, 'getUserLatestTweets']);

});
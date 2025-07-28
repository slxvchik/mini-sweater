<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (){

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    })->middleware('throttle:5,1');

    Route::prefix('users')->controller(UserController::class)->group(function (){
        Route::get('/', 'index');
        Route::get('/{username}', 'show');
    });
    
    Route::prefix('follows')->controller(FollowController::class)->group(function (){

        Route::get('/','index');
        Route::get('/follow', 'show');

        Route::get('/{user_id}/followers', 'followers');
        Route::get('/{user_id}/following', 'following');
        Route::post('/follow', 'follow')->middleware('auth:sanctum');
        Route::delete('/unfollow', 'unfollow')->middleware('auth:sanctum');
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
        Route::get('/feed', 'showFollowingTweets')->middleware('auth:sanctum');
        Route::get('/{tweet_id}', 'show');
        Route::put('/{tweet_id}', 'update')->middleware('auth:sanctum');
        Route::delete('/{tweet_id}', 'destroy')->middleware('auth:sanctum');

        Route::get('/user/{user_id}', 'showUserTweets');
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

        // Tweet likes
        Route::get('/tweet/{tweet_id}', 'showTweetLikes');

        // Comment likes
        Route::get('/comment/{comment_id}', 'showCommentLikes');
    });

});
<?php

namespace App\Http\Controllers;

use App\Http\Resources\Tweets\TweetListResource;
use App\Services\TweetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetController extends BaseController
{
    protected TweetService $tweetService;

    public function __construct(TweetService $tweetService) {
        $this->tweetService = $tweetService;
    }

    // Last tweets all users
    public function index(Request $request): JsonResponse {
        
        $validator = Validator::make($request->all(), [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'in:created_at,likes_count,comments_count'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc']
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }
    
        $tweets = $this->tweetService->getTweets(
            Auth::id(),
            $request->input('per_page') ?? 20,
            $request->input('page') ?? 1,
            $request->input('sort_by') ?? 'created_at',
            $request->input('sort_order') ?? 'desc',
        );
        
        return $this->sendResponse($tweets);
    }

    // Last user tweets
    public function showUserTweets(Request $request, int $userId): JsonResponse {

        $validator = Validator::make(
            array_merge($request->all(), ['user_id' => $userId]), 
            ['user_id' => ['required', 'integer'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'in:created_at,likes_count,comments_count'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc']]
        );
        
        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }
    
        $tweets = $this->tweetService->getUserTweets(
            Auth::id(),
            $userId,
            $request->input('per_page') ?? 20,
            $request->input('page') ?? 1,
            $request->input('sort_by') ?? 'created_at',
            $request->input('sort_order') ?? 'desc',
        );
        
        return $this->sendResponse($tweets);
    }

    // Last tweets following users
    // public function showFollowingTweets(Request $request): JsonResponse {
    //     // getFollowingTweets
    // }

    public function store(Request $request): JsonResponse {
    
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'text' => ['required','string', 'max:16384'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $tweet = $this->tweetService->create(
            $userId, 
            $request->input('text')
        );
        
        return $this->sendResponse($tweet,201);
    }

    public function show(Request $request, int $tweetId): JsonResponse {
        
        $tweet = $this->tweetService->getById(
            $tweetId, 
            Auth::id()
        );
        
        return $this->sendResponse($tweet);
    }

    public function destroy(Request $request, int $tweetId): JsonResponse {

        $userId = Auth::id();

        if (!$this->tweetService->isUserTweet($userId, $tweetId)) {
            return $this->sendError('This tweet does not belong to the user', 403);
        }

        $this->tweetService->destroy($tweetId);

        return $this->sendResponse(null, 200, 'Tweet and all related data deleted successfully');
    }

    public function update(Request $request, int $tweetId): JsonResponse {

        $userId = Auth::id();

        if (!$this->tweetService->isUserTweet($userId, $tweetId)) {
            return $this->sendError('This tweet does not belong to the user', 401);
        }

        $validator = Validator::make($request->all(), [
            'text' => ['required','string', 'max:16384'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }
        
        $tweet = $this->tweetService->update($tweetId, $request->input('text'));
        
        return $this->sendResponse($tweet);
    }
}

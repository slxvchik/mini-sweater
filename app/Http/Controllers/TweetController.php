<?php

namespace App\Http\Controllers;

use App\Http\Resources\Tweets\TweetListResource;
use App\Services\TweetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetController extends BaseController
{
    protected TweetService $tweetService;

    public function __construct(TweetService $tweetService) {
        $this->tweetService = $tweetService;
    }

    public function index(Request $request): JsonResponse {
        
        $validator = Validator::make($request->all(), [
            'user_id' => ['sometimes', 'integer'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100']
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }
    
        $input = $request->all();

        $tweets = $this->tweetService->getTweets($input);
        
        return $this->sendResponse($tweets);
    }

    public function store(Request $request): JsonResponse {
    
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'text' => ['required','string', 'max:16384'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $input = $request->all();

        $tweet = $this->tweetService->create($userId, $input['text']);
        
        return $this->sendResponse($tweet,201);
    }

    public function show(Request $request, int $tweetId): JsonResponse {
        
        $tweet = $this->tweetService->getById($tweetId);

        if ($tweet === null) {
            return $this->sendError('Tweet not found', 404);
        }
        
        return $this->sendResponse($tweet);
    }

    public function destroy(Request $request, int $tweetId): JsonResponse {

        $userId = Auth::id();

        if (!$this->tweetService->isUserTweet($userId, $tweetId)) {
            return $this->sendError('This tweet does not belong to the user', 401);
        }

        if ($this->tweetService->destroy($tweetId)) {
            return $this->sendResponse(null, 200, 'Tweet has been deleted');
        }

        return $this->sendError( 'Server cannot delete user', 500);
    }

    public function update(Request $request, int $tweetId): JsonResponse {

        $userId = Auth::id();

        if (!$this->tweetService->isUserTweet($userId, $tweetId)) {
            return $this->sendError('This tweet does not belong to the user', 401);
        }

        $validated = $request->validate([
            'text' => ['required','string', 'max:16384'],
        ]);
        
        $tweet = $this->tweetService->update($tweetId, $validated['text']);
        
        return $this->sendResponse($tweet);
    }
}

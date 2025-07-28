<?php

namespace App\Http\Controllers;

use App\Enums\LikeableType;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeController extends BaseController
{
    protected LikeService $likeService;

    public function __construct(LikeService $likeService) {
        $this->likeService = $likeService;
    }

    public function index(Request $request): JsonResponse {
        $likes = $this->likeService->getAll();
        return $this->sendResponse($likes);
    }

    public function storeLikeTweet(Request $request): JsonResponse {
    
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'tweet_id' => ['integer', 'required'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $like = $this->likeService->create(
            $userId,
            $request->input('tweet_id'),
            LikeableType::TWEET
        );

        return $this->sendResponse($like, 201);
    }

    public function storeLikeComment(Request $request): JsonResponse {
        
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'comment_id' => ['integer', 'required'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $like = $this->likeService->create(
            $userId, 
            $request->input('comment_id'), 
            LikeableType::COMMENT
        );

        return $this->sendResponse($like, 201);
    }

    public function show(int $likeId): JsonResponse {

        $like = $this->likeService->getLikeById($likeId);

        return $this->sendResponse($like);
    }

    public function destroy(int $likeId): JsonResponse {
        
        $userId = Auth::id();
        
        if (!$this->likeService->isUserLike($userId, $likeId)) {
            return $this->sendError("This like does not belong to the user", 401);
        }

        if ($this->likeService->destroy($likeId)) {
            return $this->sendResponse(null, 200, 'Unliked');
        }

        return $this->sendError('Server cannot delete like', 500);
    }

    public function showTweetLikes(int $tweetId): JsonResponse {
        
        $likes = $this->likeService->getTweetLikes($tweetId);

        return $this->sendResponse($likes);
    }

    public function showCommentLikes(int $tweetId): JsonResponse {
        
        $likes = $this->likeService->getCommentLikes($tweetId);

        return $this->sendResponse($likes);
    }
}

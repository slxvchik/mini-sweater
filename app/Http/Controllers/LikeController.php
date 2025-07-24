<?php

namespace App\Http\Controllers;

use App\Enums\LikeableType;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        $validated = $request->validate([
            'tweet_id' => ['integer', 'required'],
        ]);

        $like = $this->likeService->create($userId, $validated['tweet_id'], LikeableType::TWEET);

        return $this->sendResponse($like, 201);
    }

    public function storeLikeComment(Request $request): JsonResponse {
        
        $userId = Auth::id();

        $validated = $request->validate([
            'comment_id' => ['integer', 'required'],
        ]);

        $like = $this->likeService->create($userId, $validated['comment_id'], LikeableType::COMMENT);

        return $this->sendResponse($like, 201);
    }

    public function show(Request $request, int $likeId) {
        $like = $this->likeService->getById($likeId);
        return $this->sendResponse($like);
    }

    public function destroy(Request $request, int $likeId): JsonResponse {
        
        $userId = Auth::id();

        if (!$this->likeService->isUserLike($userId, $likeId)) {
            return $this->sendError("This like does not belong to the user", 401);
        }

        if ($this->likeService->destroy($likeId)) {
            return $this->sendResponse(null, 200, ' has been unliked');
        }

        return $this->sendError('Server cannot delete like', 500);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentListResource;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends BaseController
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService) {
        $this->commentService = $commentService;
    }

    public function index(Request $request): JsonResponse {
        $comments = $this->commentService->getAll();
        return $this->sendResponse($comments);
    }

    public function store(Request $request): JsonResponse {
    
        $userId = Auth::id();

        $validated = $request->validate([
            'tweet_id' => ['required', 'integer'],
            'text' => ['required'],
        ]);

        $comment = $this->commentService->create($userId, $validated['tweet_id'], $validated['text']);
        return $this->sendResponse($comment, 201);
    }

    public function show(Request $request, int $commentId): JsonResponse {
        $comment = $this->commentService->getById($commentId);
        return $this->sendResponse($comment);
    }

    public function update(Request $request, int $commentId): JsonResponse {

        $userId = Auth::id();

        if (!$this->commentService->isUserComment($userId, $commentId)) {
            return $this->sendError("This comment does not belong to the user", 401);
        }

        $validated = $request->validate([
            'text' => ['required'],
        ]);

        $comment = $this->commentService->update($commentId, $validated['text']);

        return $this->sendResponse($comment);
    }

    public function destroy(Request $request, int $commentId): JsonResponse {
        
        $userId = Auth::id();

        if (!$this->commentService->isUserComment($userId, $commentId)) {
            return $this->sendError("This comment does not belong to the user", 401);
        }

        if ($this->commentService->destroy($commentId)) {
            return $this->sendResponse(null);
        }

        return $this->sendError('Server cannot delete user', 500);
    }

    public function tweetComments(Request $request, int $tweetId): JsonResponse {
        
        $comments = $this->commentService->getAllByTweetId($tweetId);
        
        return $this->sendResponse(CommentListResource::collection($comments));
    }
}

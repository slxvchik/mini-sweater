<?php

namespace App\Http\Controllers;

use App\Http\Resources\Comments\CommentListResource;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends BaseController
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService) {
        $this->commentService = $commentService;
    }

    public function index(): JsonResponse {
        $comments = $this->commentService->getAll();
        return $this->sendResponse($comments);
    }

    public function store(Request $request): JsonResponse {
    
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'tweet_id' => ['required', 'integer'],
            'text' => ['required'],
            'parent_id' => ['sometimes', 'integer']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $comment = $this->commentService->create(
            $userId,
            $request->input('tweet_id'),
            $request->input('parent_id'),
            $request->input('text')
        );

        return $this->sendResponse($comment, 201);
    }

    public function show(int $commentId): JsonResponse {

        $comment = $this->commentService->getCommentById($commentId);

        return $this->sendResponse($comment);
    }

    public function update(Request $request, int $commentId): JsonResponse {

        $userId = Auth::id();

        if (!$this->commentService->isUserComment($userId, $commentId)) {
            return $this->sendError("This comment does not belong to the user", 401);
        }

        $validator = Validator::make($request->all(), [
            'text' => ['required', 'min:1'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $comment = $this->commentService->update($commentId, $request->input('text'));

        return $this->sendResponse($comment);
    }

    public function destroy(int $commentId): JsonResponse {
        
        $userId = Auth::id();

        if (!$this->commentService->isUserComment($userId, $commentId)) {
            return $this->sendError("This comment does not belong to the user", 401);
        }

        $this->commentService->destroy($commentId);

        return $this->sendResponse(null, 200, 'Comment has been deleted');
    }

    public function tweetComments(Request $request, int $tweetId): JsonResponse {
        
        $validator = Validator::make(array_merge($request->all()), [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', 'in:created_at,likes_count'],
            'sort_order' => ['sometimes', 'string', 'in:asc,desc']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }
    
        $comments = $this->commentService->getTweetComments(
            Auth::id(),
            $tweetId,
            $request->input('per_page', 20),
            $request->input('page', 1),
            $request->input('sort_by', 'created_at'),
            $request->input('sort_order', 'desc')
        );

        return $this->sendResponse(CommentListResource::collection($comments));
    }
}

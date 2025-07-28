<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FollowController extends BaseController
{
    private FollowService $followService;

    public function __construct(FollowService $followService) {
        $this->followService = $followService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse {
        $follows = $this->followService->getAllFollows();
        return $this->sendResponse($follows);
    }

    public function show(Request $request) {

        $validator = Validator::make($request->all(), [
            'followed_user_id' => ['required', 'integer'],
            'follower_user_id' => ['required', 'integer']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $follow = $this->followService->getFollow(
            $request->input('follower_user_id'),
            $request->input('followed_user_id')
        );
        return $this->sendResponse($follow);
    }

    /**
     * Display the specified resource.
     */
    public function follow(Request $request): JsonResponse {
        
        $authUserId = Auth::id();

        $validator = Validator::make($request->all(), [
            'followed_user_id' => ['required', 'integer']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $follow = $this->followService->follow($authUserId, $request->input('followed_user_id'));
        
        return $this->sendResponse($follow);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function unfollow(Request $request): JsonResponse {
        
        $authUserId = Auth::id();

        $validator = Validator::make($request->all(), [
            'followed_user_id' => ['required', 'integer']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $follow = $this->followService->unfollow($authUserId, $request->input('followed_user_id'));
        
        return $this->sendResponse($follow);
    }

    public function followers(int $userId): JsonResponse {

        $followers = $this->followService->getFollowers($userId);

        return $this->sendResponse($followers);
    }

    public function following(int $userId): JsonResponse {
        
        $followed = $this->followService->getFollowed($userId);

        return $this->sendResponse($followed);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Services\TweetService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse {
        $users = $this->userService->getAllUsers();
        return $this->sendResponse($users);
    }

    public function show(Request $request, string $username): JsonResponse {
        
        $user = $this->userService->getUserByUsername($username);

        $userResource = new UserResource($user);

        return $this->sendResponse([
            'user' => $userResource
        ]);
    }

}

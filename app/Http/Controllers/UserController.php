<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Services\TweetService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function show(string $username): JsonResponse {
        
        $validator = Validator::make(['username' => $username], [
            'username' => ['required', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z0-9_]{4,32}$/i'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $user = $this->userService->getUserByUsername($username);

        $userResource = new UserResource($user);

        return $this->sendResponse([
            'user' => $userResource
        ]);
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends BaseController
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show(Request $request): JsonResponse {
        
        $user = $request->user();

        return $this->sendResponse($user);
    }

    public function update(Request $request): JsonResponse {

        $validated = $request->validate([
            'bio' => ['required', 'nullable', 'max:4096'],
            'birthday' => ['required', 'nullable', 'dateformat:d.m.Y']
        ]);
        
        $userId = Auth::id();

        $user = $this->userService->update(
            $userId, 
            $validated['bio'],
            $validated['birthday']
        );

        return $this->sendResponse($user);
    }

    public function partitialUpdate(Request $request): JsonResponse {
        
        $validated = $request->validate([
            'bio' => ['sometimes', 'nullable', 'max:4096',],
            'birthday' => ['sometimes', 'nullable', 'dateformat:d.m.Y']
        ]);

        $userId = Auth::id();

        $user = $this->userService->partitialUpdate(
            $userId, 
            $validated
        );
        
        return $this->sendResponse($user);
    }

    public function destroy(Request $request): JsonResponse {

        $userId = Auth::id();

        if ($this->userService->destroy($userId)) {
            return $this->sendResponse(null, 200, 'User has been deleted');
        }

        return $this->sendError('Server cannot delete user', 500);
    }

    public function changeEmail(Request $request): JsonResponse {

        $validated = $request->validate([
            'old_email' => ['required', 'email'],
            'new_email' => ['required',  'email'],
            'password' => ['required', 'min:6']
        ]);

        $userId = Auth::id();

        $user = $this->userService->updateEmail(
            $userId, 
            $validated['old_email'], 
            $validated['new_email'], 
            $validated['password']
        );

        return $this->sendResponse($user);
    }

    public function changeUsername(Request $request): JsonResponse {

        $validated = $request->validate([
            'old_username' => ['required', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'new_username' => ['required',  'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'password' => ['required', 'min:6']
        ]);

        $userId = Auth::id();

        $user = $this->userService->updateUsername($userId, $validated['old_username'], $validated['new_username'], $validated['password']);

        return $this->sendResponse($user);
    }

    public function changePassword(Request $request): JsonResponse {
        
        $validated = $request->validate([
            'old_password' => ['required', 'min:6'],
            'new_password' => ['required', 'min:6']
        ]);

        $userId = Auth::id();

        $user = $this->userService->changePassword($userId, $validated['old_password'], $validated['new_password']);

        return $this->sendResponse($user);
    }
}

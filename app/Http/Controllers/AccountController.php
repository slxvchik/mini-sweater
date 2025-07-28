<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $validator = Validator::make($request->all(), [
            'bio' => ['required', 'nullable', 'max:4096'],
            'birthday' => ['required', 'nullable', 'dateformat:d.m.Y']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }
        
        $userId = Auth::id();

        $user = $this->userService->update(
            $userId, 
            $request->input('bio'),
            $request->input('birthday')
        );

        return $this->sendResponse($user);
    }

    public function partitialUpdate(Request $request): JsonResponse {
        
        $validator = Validator::make($request->all(), [
            'bio' => ['sometimes', 'nullable', 'max:4096',],
            'birthday' => ['sometimes', 'nullable', 'dateformat:d.m.Y']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $userId = Auth::id();

        $user = $this->userService->partitialUpdate(
            $userId, 
            $validator->validated()
        );
        
        return $this->sendResponse($user);
    }

    public function destroy(): JsonResponse {

        $userId = Auth::id();

        if ($this->userService->destroy($userId)) {
            return $this->sendResponse(null, 200, 'User has been deleted');
        }

        return $this->sendError('Server cannot delete user', 500);
    }

    public function changeEmail(Request $request): JsonResponse {

        $validator = Validator::make($request->all(), [
            'old_email' => ['required', 'email'],
            'new_email' => ['required',  'email'],
            'password' => ['required', 'min:6']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $userId = Auth::id();

        $user = $this->userService->updateEmail(
            $userId, 
            $request->input('old_email'), 
            $request->input('new_email'), 
            $request->input('password')
        );

        return $this->sendResponse($user);
    }

    public function changeUsername(Request $request): JsonResponse {

        $validator = Validator::make($request->all(), [
            'old_username' => ['required', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z0-9_]{4,32}$/i'],
            'new_username' => ['required',  'max:255', 'regex:/^[a-zA-Z][a-zA-Z0-9_]{4,32}$/i'],
            'password' => ['required', 'min:6']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $userId = Auth::id();

        $user = $this->userService->updateUsername(
            $userId, 
            $request->input('old_username'), 
            $request->input('new_username'), 
            $request->input('password')
        );

        return $this->sendResponse($user);
    }

    public function changePassword(Request $request): JsonResponse {
        
        $validator = Validator::make($request->all(), [
            'old_password' => ['required', 'min:6'],
            'new_password' => ['required', 'min:6']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $userId = Auth::id();

        $user = $this->userService->changePassword(
            $userId, 
            $request->input('old_password'), 
            $request->input('new_password')
        );

        return $this->sendResponse($user);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{

    protected UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    // Можно зарегистрировать пользователей, когда аутентифицировался
    public function register(Request $request): JsonResponse {

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'unique:users', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
            'remember_me' => ['required', 'boolean']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        $input = $request->all();

        $user = $this->userService->create(
            $request->input('username'), 
            $request->input('email'), 
            $request->input('password')
        );

        Auth::login($user, $request->input('remember_me'));
        $request->session()->regenerate();


        return $this->sendResponse($user, 201, 'User successfully created');
    }

    public function login(Request $request): JsonResponse {
        
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'password' => ['required', 'min:6'],
            'remember_me' => ['required', 'boolean']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', 422, $validator->errors());
        }

        if(!Auth::attempt([
            'username' => $request->input('username'), 
            'password' => $request->input('password')
        ], $request->input('remember_me'))) {
            return $this->sendError(
                'The provided credentials are incorrect', 
                401
            );
        }

        $success = $request->session()->regenerate();

        return $this->sendResponse($success);
    }

    public function logout(Request $request): JsonResponse {

        if (!Auth::check()) {
            return $this->sendError('No active session', 401);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->sendResponse(null, 200, 'Logout success');
    }
}

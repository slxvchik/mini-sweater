<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index() {
        $users = $this->userService->getAllUsers();
        return response()->json($users);
    }

    public function store(Request $request) {
        
        $validated = $request->validate([
            'username' => ['required', 'unique:users', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
        ]);

        $user = $this->userService->createUser($validated['username'], $validated['email'], $validated['password']);

        return response()->json($user, 201);
    }

    public function get(Request $request, string $username) {
        
        $validated = validator(['username' => $username], [
            'username' => ['required', 'string', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i']
        ])->validate();
        
        $user = $this->userService->findUserByUsername($validated['username']);

        return response()->json($user,200);
    }

    public function delete(Request $request) {
        
        $validate = $request->validate([
            'user_id' => ['required', 'integer']
        ]);

        if ($this->userService->deleteUser($validate['user_id'])) {
            return response()->json('', 200);
        }

        throw new HttpException(500, "Server cannot delete user");
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'bio' => ['required', 'nullable', 'max:4096',],
            'birthday' => ['required', 'nullable', 'date']
        ]);

        $user = $this->userService->update(
            $validated['user_id'],
            $validated['bio'],
            $validated['birthday']
        );

        return response()->json($user,200);
    }

    public function changeEmail(Request $request) {
        
        $validated = $request->validate([
            'old_email' => ['required', 'email'],
            'new_email' => ['required',  'email'],
            'password' => ['required', 'min:6']
        ]);

        $user = $this->userService->updateEmail($validated['old_email'], $validated['new_email'], $validated['password']);

        return response()->json($user, 200);
    }

    public function changeUsername(Request $request) {

        $validated = $request->validate([
            'old_username' => ['required', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'new_username' => ['required',  'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'password' => ['required', 'min:6']
        ]);

        $user = $this->userService->updateUsername($validated['old_username'], $validated['new_username'], $validated['password']);

        return response()->json($user, 200);
    }

    public function changePassword(Request $request) {
        
        $validated = $request->validate([
            'username' => ['required', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'email' => ['required',  'email'],
            'old_password' => ['required', 'min:6'],
            'new_password' => ['required', 'min:6']
        ]);
    
        $user = $this->userService->changePassword($validated['email'], $validated['username'], $validated['old_password'], $validated['new_password']);

        return response()->json($user, 200);
    }
}

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
        return response()->json($users, 200);
    }

    public function store(Request $request) {
        
        $validated = $request->validate([
            'username' => ['required', 'unique:users', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
        ]);

        $user = $this->userService->create($validated['username'], $validated['email'], $validated['password']);

        return response()->json($user, 201);
    }

    public function show(Request $request, string $username) {
        
        $validated = validator(['username' => $username], [
            'username' => ['required', 'string']
        ])->validate();
        
        $user = $this->userService->findUserByUsername($validated['username']);

        return response()->json($user,200);
    }

    public function update(Request $request, string $username) {

        $validated = $request->validate([
            'bio' => ['required', 'nullable', 'max:4096',],
            'birthday' => ['required', 'nullable', 'dateformat:d.m.Y']
        ]);

        $user = $this->userService->update(
            $username, 
            $validated['bio'],
            $validated['birthday']
        );

        return response()->json($user, 200);
    }

    public function partitialUpdate(Request $request, string $username) {
        
        $validated = $request->validate([
            'bio' => ['sometimes', 'nullable', 'max:4096',],
            'birthday' => ['sometimes', 'nullable', 'dateformat:d.m.Y']
        ]);

        $bio = $validated['bio'] ?? null;
        $birthday = $validated['birthday'] ?? null;
        
        $user = $this->userService->partitialUpdate(
            $username, 
            $bio,
            $birthday
        );
        
        return response()->json($user, 200);
    }

    public function destroy(Request $request, string $username) {

        if ($this->userService->destroy($username)) {
            return response()->json(null, 200);
        }

        throw new HttpException(500, "Server cannot delete user");
    }

    public function changeEmail(Request $request, string $username) {

        $validated = $request->validate([
            'old_email' => ['required', 'email'],
            'new_email' => ['required',  'email'],
            'password' => ['required', 'min:6']
        ]);

        $user = $this->userService->updateEmail($username, $validated['old_email'], $validated['new_email'], $validated['password']);

        return response()->json($user, 200);
    }

    public function changeUsername(Request $request, string $username) {

        $validated = $request->validate([
            'old_username' => ['required', 'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'new_username' => ['required',  'max:255', 'regex:/[a-zA-Z][a-zA-Z0-9_]{4,32}/i'],
            'password' => ['required', 'min:6']
        ]);

        $user = $this->userService->updateUsername($username, $validated['old_username'], $validated['new_username'], $validated['password']);

        return response()->json($user, 200);
    }

    public function changePassword(Request $request, string $username) {
        
        $validated = $request->validate([
            'old_password' => ['required', 'min:6'],
            'new_password' => ['required', 'min:6']
        ]);
    
        $user = $this->userService->changePassword($username, $validated['old_password'], $validated['new_password']);

        return response()->json($user, 200);
    }
}

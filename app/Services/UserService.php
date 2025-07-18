<?

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService {

    public function getAllUsers() {
        return User::all();
    }

    public function findUserByUsername($username): User {
        $user = User::where('username', $username)->firstOrFail();
        
        // if (is_null($user)) {
        //     throw new HttpException(404, "User not found");
        // }
        
        return $user;
    }

    public function getUserByEmail($arr) {

    }

    public function createUser($username, $email, $password): User {
        return User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password)
        ]);
    }

    public function update($userId, $bio, $birthday): User {
        
        $user = User::where('id', $userId)->firstOrFail();
        
        // if (is_null($user)) {
        //     throw new HttpException(404, "User not found");
        // }

        $user->bio = $bio;
        $user->birthday = $birthday;
        $user->save();

        return $user;
    }

    public function deleteUser($userId) {

        $user = User::where('id', $userId)->firstOrFail();

        return $user->delete();
    }

    public function updateEmail($oldEmail, $newEmail, $password): User {

        if ($oldEmail === $newEmail) {
            throw new HttpException(401, "Old and New emails are the same");
        }

        if (User::where('email', $newEmail)->exists()) {
            throw new HttpException(409, "Email already has been taken");
        }

        $user = User::where('email', $oldEmail)->firstOrFail();
        
        // if (is_null($user)) {
        //     throw new HttpException(404, "Email is not registered");
        // }
        
        if (!Hash::check($password, $user->password)) {
            throw new HttpException(401, "Password is wrong");
        }

        $user->email = $newEmail;
        $user->save();
        
        return $user;
    }

    public function updateUsername($oldUsername, $newUsername, $password): User {
        
        if ($oldUsername === $newUsername) {
            throw new HttpException(401, "Old and New usernames are the same");
        }

        if (User::where('username', $oldUsername)->exists()) {
            throw new HttpException(409, "Username already has been taken");
        }

        $user = User::where('username', $oldUsername)->firstOrFail();
        
        // if (is_null($user)) {
        //     throw new HttpException(404, "Username is not registered");
        // }
        
        if (!Hash::check($password, $user->password)) {
            throw new HttpException(401, "Password is wrong");
        }

        $user->username = $newUsername;
        $user->save();
        
        return $user;
    }

    public function changePassword($email, $username, $oldPassword, $newPassword): User {
        $user = User::where('email', $email)->where('username', $username)->firstOrFail();

        // if (is_null($user)) {
        //     throw new HttpException(409, "Username or email is uncorrect");
        // }

        if (!Hash::check($oldPassword, $user->password)) {
            throw new HttpException(401, "Password is wrong");
        }

        if ($oldPassword === $newPassword) {
            throw new HttpException(401, "Old password equal new password");
        }

        $user->password = $newPassword;
        $user->save();
        
        return $user;
    }

}
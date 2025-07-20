<?

namespace App\Repository;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository {

    public function getAll() {
        return User::all();
    }

    public function findUserById(int $userId) {
        return User::where('id', $userId)->firstOrFail();
    }

    public function findUserByUsername(string $username): User {
        return User::where('username', $username)->firstOrFail();
    }

    public function createUser(string $username, string $email, string $password): User {
        return User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password)
        ]);
    }

    public function destroy(string $username): bool | null {
        
        $user = $this->findUserByUsername($username);

        return $user->delete();
    }

    public function userExistsByUsername(string $username): bool {
        return User::where('username', $username)->exists();
    }

    public function userExistsByEmail(string $email): bool {
        return User::where('email', $email)->exists();
    }
}
<?

namespace App\Repository;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserRepository {

    public function getAll(): Collection {
        return User::all();
    }

    public function findUserById(int $userId): ?User {
        return User::where('id', $userId)->first();
    }

    public function findUserByUsername(string $username): ?User {
        return User::where('username', $username)->first();
    }

    public function createUser(string $username, string $email, string $password): User {
        return User::create([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function destroy(int $userId): ?bool {
        
        $user = $this->findUserById($userId);

        return $user->delete();
    }

    public function isUserExistsByUsername(string $username): bool {
        return User::where('username', $username)->exists();
    }

    public function isUserExistsByEmail(string $email): bool {
        return User::where('email', $email)->exists();
    }

    public function isUserExistsById(int $userId): bool {
        return User::where('id', $userId)->exists();
    }
}
<?

namespace App\Services;

use App\Exceptions\AuthException;
use App\Exceptions\ConflictException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\DB;

class UserService {

    protected UserRepository $userRepository;

    
    public function __construct(
            UserRepository $userRepository,
        ) {

        $this->userRepository = $userRepository;
    }

    public function getAllUsers(): Collection {
        return $this->userRepository->getAll();
    }

    public function getUserByUsername(string $username): User {
        $user = $this->userRepository->findUserByUsername($username);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function getUserById(int $userId): User {
        $user = $this->userRepository->findUserById($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function isUserExistsById($userId): bool {
        return $this->userRepository->isUserExistsById($userId);
    }

    public function isUserExistsByUsername(string $username): bool {
        return $this->userRepository->isUserExistsByUsername($username);
    }

    public function create(string $username, string $email, string $password): User {
        return DB::transaction(function () use ($username, $email, $password) {
            return $this->userRepository->createUser($username, $email, $password);
        });
    }

    public function update(int $userId, string $bio, string $birthday): User {
        
        $user = $this->getUserById($userId);

        $user->bio = $bio;
        $user->birthday = $birthday;
        $user->save();

        return $user;
    }

    public function partitialUpdate(int $userId, array $attributes): User {
        
        $user = $this->getUserById($userId);

        $allowedFields = ['bio', 'birthday'];

        foreach ($attributes as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $user->{$key} = $value;
            }
        }

        $user->save();
        
        return $user;
    }

    public function destroy(int $userId): ?bool {
        return $this->userRepository->destroy($userId);
    }

    public function updateEmail(int $userId, string $oldEmail, string $newEmail, string $password): User {

        if ($oldEmail === $newEmail) {
            throw new ValidationException('Old and new emails are the same');
        }

        if ($this->userRepository->isUserExistsByEmail($newEmail)) {
            throw new ConflictException('Email already has been taken');
        }

        $user = $this->getUserById($userId);

        if ($user->email !== $oldEmail) {
            throw new AuthException('Old email does not match');
        }
        
        if (!Hash::check($password, $user->password)) {
            throw new AuthException('Invalid password');
        }
        
        // todo: send email to validate

        $user->email = $newEmail;
        $user->save();

        
        return $user;
    }

    public function updateUsername(int $userId, string $oldUsername, string $newUsername, string $password): User {
        
        if ($oldUsername === $newUsername) {
            throw new ValidationException('Old and new usernames are the same');
        }

        if ($this->userRepository->isUserExistsByUsername($newUsername)) {
            throw new ConflictException('Username already has been taken');
        }

        $user = $this->getUserById($userId);
        
        if ($user->username !== $oldUsername) {
            throw new AuthException('Old username does not match');
        }
        
        if (!Hash::check($password, $user->password)) {
            throw new AuthException('Invalid password');
        }

        $user->username = $newUsername;
        $user->save();
        
        return $user;
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): User {
        
        if ($oldPassword === $newPassword) {
            throw new ValidationException('Old and new passwords are the same');
        }

        $user = $this->getUserById($userId);

        if (!Hash::check($oldPassword, $user->password)) {
            throw new AuthException('Invalid password');
        }

        $user->password = $newPassword;
        $user->save();

        // User Session deleted
        
        return $user;
    }

}
<?

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;

class UserService {

    protected UserRepository $userRepository;
    protected TweetRepository $tweetRepository;

    
    public function __construct(
            UserRepository $userRepository,
            TweetRepository $tweetRepository
        ) {

        $this->userRepository = $userRepository;
        $this->tweetRepository = $tweetRepository;
    }

    public function getAllUsers(): Collection {
        return $this->userRepository->getAll();
    }

    public function getUserByUsername(string $username): User {
        return $this->userRepository->findUserByUsername($username);
    }

    public function getUserByEmail($arr) {

    }

    public function getUserById($userId): User {
        return $this->userRepository->findUserById($userId);
    }

    public function create(string $username, string $email, string $password): User {
        return $this->userRepository->createUser($username, $email, $password);
    }

    public function update(int $userId, string $bio, string $birthday): User {
        
        $user = $this->userRepository->findUserById($userId);
        
        if ($user === null) {
            throw new HttpException(404, "User not found");
        }

        $user->bio = $bio;
        $user->birthday = $birthday;
        $user->save();

        return $user;
    }

    public function partitialUpdate(int $userId, array $attributes): User {
        
        $user = $this->userRepository->findUserById($userId);

        if ($user === null) {
            throw new HttpException(404, "User not found");
        }

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
            throw new HttpException(401, "Old and New emails are the same");
        }

        if ($this->userRepository->userExistsByEmail($newEmail)) {
            throw new HttpException(409, "Email already has been taken");
        }

        $user = $this->userRepository->findUserById($userId);

        if ($user === null) {
            throw new HttpException(404, "User not found");
        }
        
        if ($user->email !== $oldEmail) {
            throw new HttpException(401, "Old mail does not match");
        }
        
        if (!Hash::check($password, $user->password)) {
            throw new HttpException(401, "Password is wrong");
        }
        
        // todo: send email to validate

        $user->email = $newEmail;
        $user->save();

        
        return $user;
    }

    public function updateUsername(int $userId, string $oldUsername, string $newUsername, string $password): User {
        
        if ($oldUsername === $newUsername) {
            throw new HttpException(401, "Old and New usernames are the same");
        }

        if ($this->userRepository->userExistsByUsername($newUsername)) {
            throw new HttpException(409, "Username already has been taken");
        }

        $user = $this->userRepository->findUserById($userId);

        if ($user === null) {
            throw new HttpException(404, "User not found");
        }
        
        if ($user->username !== $oldUsername) {
            throw new HttpException(401, "Old username does not match");
        }
        
        if (!Hash::check($password, $user->password)) {
            throw new HttpException(401, "Password is wrong");
        }

        $user->username = $newUsername;
        $user->save();
        
        return $user;
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): User {
        
        $user = $this->userRepository->findUserById($userId);

        if ($user === null) {
            throw new HttpException(404, "User not found");
        }

        if (!Hash::check($oldPassword, $user->password)) {
            throw new HttpException(401, "Password is wrong");
        }

        if ($oldPassword === $newPassword) {
            throw new HttpException(401, "Old password equal new password");
        }

        $user->password = $newPassword;
        $user->save();

        // User Session deleted
        
        return $user;
    }

}
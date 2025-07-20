<?

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

    public function findUserByUsername(string $username): User {

        return $this->userRepository->findUserByUsername($username);
    }

    public function getUserByEmail($arr) {

    }

    public function create(string $username, string $email, string $password): User {
        
        return $this->userRepository->createUser($username, $email, $password);
    }

    public function update(string $username, string $bio, string $birthday): User {
        
        $user = $this->userRepository->findUserByUsername($username);
        
        $user->bio = $bio;
        $user->birthday = $birthday;
        $user->save();

        return $user;
    }

    public function partitialUpdate(string $username, string $bio = null, string $birthday = null): User {
        
        $user = $this->userRepository->findUserByUsername($username);

        if (!is_null($bio)) {
            $user->bio = $bio;
        }
        if (!is_null($birthday)) {
            $user->birthday = $birthday;
        }

        $user->save();
        
        return $user;
    }

    public function destroy(string $username): bool | null {

        return $this->userRepository->destroy($username);
    }

    public function updateEmail(string $username, string $oldEmail, string $newEmail, string $password): User {

        if ($oldEmail === $newEmail) {
            throw new HttpException(401, "Old and New emails are the same");
        }

        if ($this->userRepository->userExistsByEmail($newEmail)) {
            throw new HttpException(409, "Email already has been taken");
        }

        $user = $this->userRepository->findUserByUsername($username);
        
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

    public function updateUsername(string $username, string $oldUsername, string $newUsername, string $password): User {
        
        if ($oldUsername === $newUsername) {
            throw new HttpException(401, "Old and New usernames are the same");
        }

        if ($this->userRepository->userExistsByUsername($newUsername)) {
            throw new HttpException(409, "Username already has been taken");
        }

        $user = $this->userRepository->findUserByUsername($username);
        
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

    public function changePassword(string $username, string $oldPassword, string $newPassword): User {
        
        $user = $this->userRepository->findUserByUsername($username);

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
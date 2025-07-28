<?

namespace App\Services;

use App\Exceptions\ConflictException;
use App\Exceptions\FollowNotFound;
use App\Exceptions\UserNotFoundException;
use App\Models\Follow;
use App\Repository\UserRepository;
use App\Repository\FollowRepository;
use Illuminate\Database\Eloquent\Collection;

class FollowService {

    private UserRepository $userRepository;
    private FollowRepository $followRepository;
    public function __construct(UserRepository $userRepository, FollowRepository $followRepository) {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }

    public function getAllFollows(): Collection {
        return $this->followRepository->getAll();
    }

    public function getFollow(int $followerId, int $followedId): Follow {
        if (!$this->userRepository->isUserExistsById($followerId)) {
            throw new UserNotFoundException('Follower user not found');
        }
        if (!$this->userRepository->isUserExistsById($followedId)) {
            throw new UserNotFoundException('Followed user not found');
        }
        $follow = $this->followRepository->findFollow($followerId, $followedId);
        if (!$follow) {
            throw new FollowNotFound();
        }
        return $follow;
    }

    public function follow(int $followerId, int $followedId): Follow {
        if (!$this->userRepository->isUserExistsById($followerId)) {
            throw new UserNotFoundException('Follower user not found');
        }
        if ($followedId === $followerId) {
            throw new ConflictException('User can not follow himself');
        }
        if (!$this->userRepository->isUserExistsById($followedId)) {
            throw new UserNotFoundException('Followed user not found');
        }
        if ($this->followRepository->isFollowExists($followerId, $followedId)) {
            throw new ConflictException('User already following');
        }
        return $this->followRepository->create($followerId, $followedId);
    }

    public function unfollow(int $followerId, int $followedId): ?bool {
        if (!$this->userRepository->isUserExistsById($followerId)) {
            throw new UserNotFoundException('Follower user not found');
        }
        if ($followedId === $followerId) {
            throw new ConflictException('User can not follow himself');
        }
        if (!$this->userRepository->isUserExistsById($followedId)) {
            throw new UserNotFoundException('Followed user not found');
        }
        if (!$this->followRepository->isFollowExists($followerId, $followedId)) {
            throw new ConflictException('User already not following');
        }
        return $this->followRepository->destroy($followerId, $followedId);
    }

    public function getFollowers(int $followedId): Collection {
        if (!$this->userRepository->isUserExistsById($followedId)) {
            throw new UserNotFoundException('Followed user not found');
        }
        return $this->followRepository->findFollowers($followedId);
    }

    public function getFollowed(int $followerId): Collection {
        if (!$this->userRepository->isUserExistsById($followerId)) {
            throw new UserNotFoundException('Follower user not found');
        }
        return $this->followRepository->findFollowed($followerId);
    }
}
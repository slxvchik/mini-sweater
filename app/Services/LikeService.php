<?

namespace App\Services;

use App\Enums\LikeableType;
use App\Exceptions\ConflictException;
use App\Exceptions\LikeNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Models\Like;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LikeService {
    protected LikeRepository $likeRepository;
    protected UserRepository $userRepository;

    public function __construct(LikeRepository $likeRepository, UserRepository $userRepository) {
        $this->likeRepository = $likeRepository;
        $this->userRepository = $userRepository;
    }

    public function getAll(): Collection {
        return $this->likeRepository->getAll();
    }

    public function create(int $userId, int $likeableId, LikeableType $likeableType): Like {

        if (!$this->userRepository->isUserExistsById($userId)) {
            throw new UserNotFoundException();
        }

        if ($this->isLikeExistsByUser($userId, $likeableId, $likeableType)) {
            throw new ConflictException('Like already exists');
        }

        return $this->likeRepository->create($userId, $likeableId, $likeableType);
    }

    public function destroy(int $likeId): ?bool {
        return $this->likeRepository->destroy($likeId);
    }

    public function getLikeById(int $likeId): Like {
        $like = $this->likeRepository->findLikeById($likeId);
        if (!$like) {
            throw new LikeNotFoundException();
        }
        return $like;
    }

    public function getTweetLikes(int $tweetId): Collection {
        return $this->likeRepository->findLikesByLikeable($tweetId, LikeableType::TWEET);
    }

    public function getCommentLikes(int $commentId): Collection {
        return $this->likeRepository->findLikesByLikeable($commentId, LikeableType::COMMENT);
    }

    public function getLikeByUser(int $userId, int $likeableId, LikeableType $likeableType): Like {
        $like = $this->likeRepository->findLikeByUser($userId, $likeableId, $likeableType);
        if (!$like) {
            throw new LikeNotFoundException();
        }
        return $like;
    }

    public function isUserLike(int $userId, int $likeId): bool {
        $like = $this->getLikeById($likeId);
        return $like->user_id === $userId;
    }

    public function isLikeExistsById(int $likeId): bool {
        return $this->likeRepository->isLikeExistsById($likeId);
    }

    public function isLikeExistsByUser(int $userId, int $likeableId, LikeableType $likeableType): bool {
        return $this->likeRepository->isLikeExistsByUser($userId, $likeableId, $likeableType);
    }

}
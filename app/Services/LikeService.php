<?

namespace App\Services;

use App\Enums\LikeableType;
use App\Models\Like;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
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

        if (!$this->userRepository->userExistsById($userId)) {
            throw new HttpException(404, 'User not found');
        }

        return $this->likeRepository->create($userId, $likeableId, $likeableType);
    }

    public function getById(int $likeId): Like {

        return $this->likeRepository->findLikeById($likeId);
    }

    public function update(int $likeId, int $tweetId, int $commentId): Like {

        if (!($tweetId === null xor $commentId === null)) {
            throw new HttpException(422, 'A "Like" cannot be both a "Tweet" and a "Comment".');
        }

        $like = $this->likeRepository->update($likeId, $tweetId, $commentId);

        return $like;
    }

    public function destroy(int $likeId): bool | null {
        return $this->likeRepository->destroy($likeId);
    }

    public function isUserLike(int $userId, int $likeId): bool {
        $like = $this->likeRepository->findLikeById($likeId);
        return $like->user_id === $userId;
    }
}
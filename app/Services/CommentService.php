<?

namespace App\Services;

use App\Http\Resources\CommentListResource;
use App\Models\Comment;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentService {

    protected CommentRepository $commentRepository;
    protected UserRepository $userRepository;

    public function __construct(CommentRepository $commentRepository, UserRepository $userRepository) {
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }

    public function getAll(): Collection {
        return $this->commentRepository->getAll();
    }

    public function create(int $userId, int $tweetId, string $text): Comment {

        if (!$this->userRepository->userExistsById($userId)) {
            throw new HttpException(404, 'User not found');
        }

        return $this->commentRepository->create($userId, $tweetId, $text);
    }

    public function getById(int $commentId): Comment {

        return $this->commentRepository->findCommentById($commentId);
    }

    public function getAllByTweetId($tweetId): Collection {
        return $this->commentRepository->findAllByTweetId($tweetId);
    }

    public function update(int $commentId, string $text): Comment {

        $comment = $this->commentRepository->update($commentId, $text);

        return $comment;
    }

    public function destroy(int $commentId): bool | null {
        return $this->commentRepository->destroy($commentId);
    }

    public function isUserComment(int $userId, int $commentId): bool {
        $comment = $this->commentRepository->findCommentById($commentId);
        return $comment->user_id === $userId;
    }
}
<?

namespace App\Services;

use App\Enums\LikeableType;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\TweetNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\ValidationException;
use App\Models\Comment;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentService {

    private TweetRepository $tweetRepository;
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;
    private LikeRepository $likeRepository;

    public function __construct(
        CommentRepository $commentRepository, 
        UserRepository $userRepository,
        LikeRepository $likeRepository,
        TweetRepository $tweetRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->tweetRepository = $tweetRepository;
    }

    public function getAll(): Collection {
        return $this->commentRepository->getAll();
    }

    public function create(int $userId, int $tweetId, ?int $parentId = null, string $text): Comment {

        if (!$this->userRepository->isUserExistsById($userId)) {
            throw new UserNotFoundException();
        }

        if (!$this->tweetRepository->tweetExists($tweetId)) {
            throw new TweetNotFoundException();
        }

        if ($parentId && !$this->commentRepository->isCommentExists($parentId)) {
            throw new CommentNotFoundException('Parent comment not found');
        }

        return $this->commentRepository->create($userId, $tweetId, $parentId, $text);
    }

    public function getCommentById(int $commentId): Comment {

        $comment = $this->commentRepository->findCommentById($commentId);

        if (!$comment) {
            throw new CommentNotFoundException();
        }

        return $comment;
    }

    public function update(int $commentId, string $text): Comment {

        if (!$this->commentRepository->isCommentExists($commentId)) {
            throw new CommentNotFoundException();
        }

        $comment = $this->commentRepository->update($commentId, $text);

        return $comment;
    }

    public function destroy(int $commentId): ?bool {
        
        if (!$this->commentRepository->isCommentExists($commentId)) {
            throw new CommentNotFoundException();
        }

        return DB::transaction(function() use ($commentId) {
            $this->likeRepository->deleteByLikeable([$commentId], LikeableType::COMMENT);
            return $this->commentRepository->destroy($commentId);
        });
    }

    public function getTweetComments(?int $currentUserId = null, int $tweetId, int $perPage = 20, int $page = 1, $sortBy = 'created_at', $sortOrder = 'desc'): Collection {

        $perPage = min(max(1, $perPage), 100);
        $page = max(1, $page);

        $sortBy = in_array($sortBy, ['created_at', 'likes_count']) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        return $this->commentRepository->findCommentsByTweetWithInfo(
            $currentUserId,
            $tweetId,
            $perPage,
            $page,
            $sortBy,
            $sortOrder
        );
    }

    public function isUserComment(int $userId, int $commentId): bool {

        if (!$this->commentRepository->isCommentExists($commentId)) {
            throw new CommentNotFoundException();
        }

        $comment = $this->commentRepository->findCommentById($commentId);

        return $comment->user_id === $userId;
    }

}
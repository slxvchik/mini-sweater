<?

namespace App\Services;

use App\Enums\LikeableType;
use App\Exceptions\TweetNotFoundException;
use App\Models\Tweet;
use App\Repository\CommentRepository;
use App\Repository\FollowRepository;
use App\Repository\LikeRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\TweetRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TweetService {

    private TweetRepository $tweetRepository;
    private LikeRepository $likeRepository;
    private CommentRepository $commentRepository;
    private FollowRepository $followRepository;

    public function __construct(
        TweetRepository $tweetRepository,
        LikeRepository $likeRepository,
        CommentRepository $commentRepository,
        FollowRepository $followRepository
        ) {

        $this->tweetRepository = $tweetRepository;
        $this->likeRepository = $likeRepository;
        $this->commentRepository = $commentRepository;
        $this->followRepository = $followRepository;
    }

    // Last tweets all users
    public function getTweets(?int $currentUserId = null, int $perPage = 20, int $page = 1, $sortBy = 'created_at', $sortOrder = 'desc') {
        
        $perPage = min(max(1, $perPage), 100);
        $page = max(1, $page);

        $sortBy = in_array($sortBy, $this->getTweetSortParams()) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';
       

        return $this->tweetRepository->findTweets(
            $currentUserId,
            $perPage,
            $page,
            $sortBy,
            $sortOrder
        );
    }

    // Last user tweets
    public function getUserTweets(?int $currentUserId = null, int $userId, int $perPage = 20, int $page = 1, string $sortBy = 'created_at', string $sortOrder = 'desc'): Collection {
        
        $perPage = min(max(1, $perPage), 100);
        $page = max(1, $page);

        $sortBy = in_array($sortBy, $this->getTweetSortParams()) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        return $this->tweetRepository->findUserTweets(
            $currentUserId,
            $userId,
            $perPage,
            $page,
            $sortBy,
            $sortOrder
        );
    }

    // Last tweets following users
    public function getFollowingTweets(int $currentUserId, int $perPage = 20, int $page = 1): Collection {

        $perPage = min(max(1, $perPage), 100);
        $page = max(1, $page);

        $followedIds = $this->followRepository->findFollowedIds($currentUserId);

        if (empty($followedIds)) {
            return new Collection();
        }

        return $this->tweetRepository->findFollowingTweets(
            $currentUserId,
            $followedIds,
            $perPage,
            $page,
        );
    }

    public function create(string $userId, string $text): Tweet {
        return $this->tweetRepository->create($userId, $text);
    }

    public function destroy(int $tweetId): ?bool {

        $tweet = $this->getById($tweetId);

        return DB::transaction(function () use ($tweet) {
            // 1. Удаляем лайки твита
            $this->likeRepository->deleteByLikeable([$tweet->id], LikeableType::TWEET);
            // 2. Удаляем лайки комментариев
            $commentIds = $this->commentRepository->findCommentIdsByTweet($tweet->id);
            $this->likeRepository->deleteByLikeable($commentIds, LikeableType::COMMENT);
            // 3. Удаляем сам твит
            return $this->tweetRepository->destroy($tweet->id);
        });
    }

    public function update(int $tweetId, string $text): Tweet { 
        
        $tweet = $this->getById($tweetId);

        $tweet->text = $text;
        $tweet->updated_at = Carbon::now();
        $tweet->save();
        
        return $tweet;
    }

    public function getById(int $tweetId, ?int $currentUserId = null): Tweet {

        $tweet = $this->tweetRepository->findTweetByIdWithCounts($tweetId, $currentUserId);

        if (!$tweet) {
            throw new TweetNotFoundException();
        }

        return $tweet;
    }

    public function isUserTweet(int $userId, int $tweetId): bool {

        $tweet = $this->getById($tweetId);

        return $tweet->user_id === $userId;
    }

    private function getTweetSortParams(): array {
        return ['created_at', 'comments_count', 'likes_count'];
    }
}
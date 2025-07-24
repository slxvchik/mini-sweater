<?

namespace App\Services;

use App\Models\Tweet;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;

class TweetService {

    protected TweetRepository $tweetRepository;
    protected UserRepository $userRepository;

    public function __construct(
        TweetRepository $tweetRepository,
        UserRepository $userRepository
        ) {

        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
    }

    public function getTweets(array $params = []): Collection {

        $perPage = min(max(1, (int)($params['per_page'] ?? 20)), 100);

        return $this->tweetRepository->findTweets($perPage, $params['page'] ?? 1, $params);
    }

    public function create(string $userId, string $text): Tweet {
        return $this->tweetRepository->create($userId, $text);
    }

    public function destroy(int $tweetId): bool | null {
        return $this->tweetRepository->destroy($tweetId);
    }

    public function update(int $tweetId, string $text): Tweet { 
        
        $tweet = $this->tweetRepository->findTweetById($tweetId);

        $tweet->text = $text;
        $tweet->save();
        
        return $tweet;
    }

    public function getById(int $tweetId): Tweet {
        return $this->tweetRepository->findTweetById($tweetId);
    }

    public function isUserTweet(int $userId, int $tweetId): bool {
        $tweet = $this->tweetRepository->findTweetById($tweetId);
        return $tweet->user_id === $userId;
    }
}
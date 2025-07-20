<?

namespace App\Services;

use App\Http\Resources\TweetResource;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

    public function getAll(): Collection {
        return $this->tweetRepository->getAll();
    }

    public function getUserLatestTweets(string $username) {
        
        $user = $this->userRepository->findUserByUsername($username);

        $tweets = $this->tweetRepository->findTweetsByUserId($user->id);

        return TweetResource::collection($tweets);
    }

    public function create(string $username, string $text): Tweet {
        
        $user = $this->userRepository->findUserByUsername($username);
        
        $tweet = $this->tweetRepository->create($user->id, $text);
        
        return $tweet;
    }

    public function destroy(int $postId): bool | null {
        return $this->tweetRepository->destroy($postId);
    }

}
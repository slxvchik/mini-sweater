<?

namespace App\Repository;

use App\Models\Tweet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetRepository {

    public function findTweets(int $perPage, int $page, $params = []): Collection {
        $query = Tweet::query()
            ->with('user:id,username')
            ->withCount(['comments', 'likes'])
            ->orderByDesc('created_at');
        foreach ($params as $column => $value) {
            if (in_array($column, ['user_id'])) {
                $query->where($column, $value);
            }
        }
        return $query->forPage($page, $perPage)->get();
    }

    public function create(int $userId, string $text): Tweet {
        return Tweet::create([
            'user_id' => $userId,
            'text' => $text,
            'created_at' => Carbon::now()
        ]);
    }

    public function findTweetById(int $tweetId): Tweet {
        $tweet = Tweet::where('id', $tweetId)->first();
        if ($tweet === null) {
            throw new HttpException(404, 'Tweet not found');
        }
        return $tweet;
    }

    public function findTweetByIdWithCounts(int $tweetId): Tweet {
        $tweet = Tweet::where('id', $tweetId)
            ->with('user:id,username')
            ->withCount(['comments', 'likes'])
            ->first();
        if ($tweet === null) {
            throw new HttpException(404, 'Tweet not found');
        }
        return $tweet;
    }

    public function destroy(int $tweetId): bool | null {
        
        $tweet = $this->findTweetById($tweetId);
        
        return $tweet->delete();
    }
}
<?

namespace App\Repository;

use App\Models\Tweet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TweetRepository {

    // Подписки (фид), пользователь, по тексту.
    
    public function findTweets(?int $currentUserId = null, int $perPage = 20, int $page = 1, $sortBy = 'created_at', $sortOrder = 'desc'): Collection {
        
        $query = Tweet::with('user:id,username')
            ->withCount(['comments', 'likes']);
        
        if ($currentUserId) {
            $query->withExists(['likes as is_liked' => function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            }]);
        }
        $query->orderBy($sortBy, $sortOrder);
        return $query->forPage($page, $perPage)->get();
    }

    public function findUserTweets(?int $currentUserId = null, int $userId, int $perPage = 20, int $page = 1, $sortBy = 'created_at', $sortOrder = 'desc'): Collection {
        
        $query = Tweet::where('user_id', $userId)
            ->with('user:id,username')
            ->withCount(['comments', 'likes']);
        
        if ($currentUserId) {
            $query->withExists(['likes as is_liked' => function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            }]);
        }

        $query->orderBy($sortBy, $sortOrder);
        return $query->forPage($page, $perPage)->get();
    }

    public function findFollowingTweets(int $currentUserId, array $followedIds, int $perPage = 20, int $page = 1): Collection {
        
        $query = Tweet::whereIn('user_id', $followedIds)
            ->with('user:id,username')
            ->withCount(['comments', 'likes']);
        
        $query->withExists(['likes as is_liked' => function($q) use ($currentUserId) {
            $q->where('user_id', $currentUserId);
        }]);

        $query->orderBy('created_at', 'desc');
        return $query->forPage($page, $perPage)->get();
    }

    public function create(int $userId, string $text): Tweet {
        return Tweet::create([
            'user_id' => $userId,
            'text' => $text,
            'created_at' => Carbon::now()
        ]);
    }

    public function findTweetById(int $tweetId): ?Tweet {
        return Tweet::where('id', $tweetId)->first();
    }

    public function findTweetByIdWithCounts(int $tweetId, ?int $currentUserId = null): ?Tweet {

        $query = Tweet::where('id', $tweetId)
            ->withCount(['comments', 'likes']);

        if ($currentUserId) {
            $query->withExists(['likes as is_liked' => function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            }]);
        }

        return $query->first();
    }

    public function destroy(int $tweetId): bool | null {
        
        $tweet = $this->findTweetById($tweetId);
        
        return $tweet->delete();
    }

    public function tweetExists(int $tweetId): bool {
        return Tweet::where('id', $tweetId)->exists();
    }
}
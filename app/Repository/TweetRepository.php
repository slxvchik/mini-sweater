<?

namespace App\Repository;

use App\Models\Tweet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TweetRepository {
    
    public function getAll(): Collection {
        return Tweet::all();
    }

    public function create(int $userId, string $text): Tweet {
        return Tweet::create([
            'user_id' => $userId,
            'text' => $text,
            'created_at' => Carbon::now()
        ]);
    }

    public function findTweetsByUserId(int $userId): Collection {
        return Tweet::where('user_id', $userId)->with('user')->latest()->get();
    }

    public function findTweetById(int $tweetId): Tweet {
        return Tweet::where('id', $tweetId)->firstOrFail();
    }

    public function destroy(int $tweetId): bool | null {
        
        $tweet = $this->findTweetById($tweetId);
        
        return $tweet->delete();
    }
}
<?

namespace App\Repository;

use App\Enums\LikeableType;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LikeRepository {
    
    public function getAll(): Collection {
        return Like::all();
    }

    public function create(int $userId, int $likeableId, LikeableType $likeableType): Like {
        return Like::create([
            'user_id' => $userId,
            'likeable_id' => $likeableId,
            'likeable_type' => $likeableType,
            'created_at' => Carbon::now()
        ]);
    }

    public function findLikesByUserId(int $userId): Collection {
        return Like::where('user_id', $userId)->with('user')->latest()->get();
    }

    public function findLikeById(int $likeId): Like {
        return Like::where('id', $likeId)->firstOrFail();
    }

    public function destroy(int $likeId): bool | null {
        
        $like = $this->findLikeById($likeId);
        
        return $like->delete();
    }

    public function update(int $likeId, $tweetId, $commentId) {

        $like = $this->findLikeById($likeId);

        $like->tweet_id = $tweetId;
        $like->comment_id = $commentId;
        $like->save();

        return $like;
    }
}
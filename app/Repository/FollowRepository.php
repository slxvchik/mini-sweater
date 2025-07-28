<?

namespace App\Repository;

use App\Models\Follow;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class FollowRepository {
    public function getAll(): Collection {
        return Follow::all();
    }
    public function create(int $followerId, int $followedId): Follow {
        return Follow::create([
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'created_at' => Carbon::now()
        ]);
    }
    public function destroy(int $followerId, int $followedId): ?bool {
        return Follow::where('follower_id', $followerId)
            ->where('followed_id', $followedId)
            ->delete();
    }
    public function findFollow(int $followerId, int $followedId): ?Follow {
        return Follow::where('follower_id', $followerId)
            ->where('followed_id', $followedId)
            ->first();
    }
    public function findFollowers(int $followedId): Collection {
        return Follow::where('followed_id', $followedId)
            ->get();
    }
    public function findFollowed(int $followerId): Collection {
        return Follow::where('follower_id', $followerId)
            ->get();
    }

    public function findFollowedIds(int $followerId): array {
        return Follow::where('follower_id', $followerId)
            ->pluck('followed_id')
            ->toArray();
    }
    public function isFollowExists(int $followerId, int $followedId): bool {
        return Follow::where('follower_id', $followerId)
            ->where('followed_id', $followedId)
            ->exists();
    }
}
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

    public function destroy(int $likeId): ?bool {
        return $this->findLikeById($likeId)->delete();
    }

    public function findLikesByUser(int $userId): Collection {
        return Like::where('user_id', $userId)->with('user')->latest()->get();
    }

    public function findLikeById(int $likeId): ?Like {
        return Like::where('id', $likeId)->first();
    }

    public function findLikeByUser(int $userId, int $likeableId, LikeableType $likeableType): ?Like {
        return Like::where('user_id', $userId)
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->first();
    }

    public function findLikesByLikeable(int $likeableId, LikeableType $likeableType): Collection {
        return Like::where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->with('user:id,username')
            ->get();
    }

    public function isLikeExistsById(int $likeId): bool {
        return Like::where('id', $likeId)->exists();
    }

    public function isLikeExistsByUser(int $userId, int $likeableId, LikeableType $likeableType): bool {
        return Like::where('user_id', $userId)
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->exists();
    }

    public function deleteByLikeable(array $likeableIds, LikeableType $type): void {
        if (count($likeableIds) === 0) return;
        // Оптимизация для разных сценариев
        count($likeableIds) > 1000
            ? $this->chunkedDelete($likeableIds, $type)
            : $this->directDelete($likeableIds, $type);
    }

    private function directDelete(array $ids, LikeableType $type): void {
        Like::where('likeable_type', $type)
            ->whereIn('likeable_id', $ids)
            ->delete();
    }

    private function chunkedDelete(array $ids, LikeableType $type, int $chunkSize = 1000): void {
        collect($ids)->chunk($chunkSize)->each(function ($chunk) use ($type) {
            $this->directDelete($chunk->toArray(), $type);
        });
    }
}
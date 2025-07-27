<?

namespace App\Repository;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentRepository {
    public function getAll(): Collection {
        return Comment::all();
    }

    public function create(int $userId, int $tweetId, ?int $parentId = null, string $text): Comment {
        return Comment::create([
            'user_id' => $userId,
            'tweet_id' => $tweetId,
            'text' => $text,
            'parent_id' => $parentId,
            'created_at' => Carbon::now()
        ]);
    }

    public function findCommentById(int $commentId): ?Comment {
        return Comment::where('id', $commentId)->first();
    }

    public function findCommentIdsByTweet(int $tweetId): array {
        return Comment::where('tweet_id', $tweetId)->pluck('id')->toArray();
    }

    public function findCommentsByTweetWithInfo(?int $currentUserId = null, int $tweetId, int $perPage, int $page, $sortBy = 'created_at', $sortOrder = 'desc'): Collection {
        $query = Comment::where('tweet_id', $tweetId)
            ->with('user')
            ->withCount('likes');
        if ($currentUserId) {
            $query->withExists(['likes as is_liked' => function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            }]);
        }
        $query->orderBy($sortBy, $sortOrder);
        return $query->forPage($page, $perPage)->get();
    }

    public function destroy(int $commentId): ?bool {
        
        $comment = $this->findCommentById($commentId);

        return $comment->delete();
    }

    public function update(int $commentId, $text): Comment {
        
        $comment = $this->findCommentById($commentId);

        $comment->text = $text;
        $comment->updated_at = Carbon::now();
        $comment->save();

        return $comment;
    }

    public function isCommentExists(int $commentId): bool {
        return Comment::where('id', $commentId)->exists();
    }
}
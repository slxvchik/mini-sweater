<?

namespace App\Repository;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository {
    public function getAll(): Collection {
        return Comment::all();
    }

    public function create(int $userId, $tweetId, string $text): Comment {
        return Comment::create([
            'user_id' => $userId,
            'tweet_id' => $tweetId,
            'text' => $text,
            'created_at' => Carbon::now()
        ]);
    }

    public function findCommentsByUserId(int $userId): Collection {
        return Comment::where('user_id', $userId)->with('user')->latest()->get();
    }

    public function findCommentById(int $commentId): Comment {
        return Comment::where('id', $commentId)->firstOrFail();
    }

    public function findAllByTweetId(int $tweetId): Collection {
        return Comment::where('tweet_id')->with('user')->get();
    }

    public function destroy(int $commentId): bool | null {
        
        $comment = $this->findCommentById($commentId);
        
        return $comment->delete();
    }

    public function update(int $commentId, $text) {
        
        $comment = $this->findCommentById($commentId);

        $comment->text = $text;

        $comment->save();

        return $comment;
    }
}
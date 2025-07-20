<?php

namespace App\Http\Controllers;

use App\Services\TweetService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetController extends Controller
{
    protected TweetService $tweetService;

    public function __construct(TweetService $tweetService) {
        $this->tweetService = $tweetService;
    }

    public function index(Request $request) {
        
        $tweets = $this->tweetService->getAll();
        
        return response()->json($tweets, 200);
    }

    public function store(Request $request) {
    
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'text' => ['required','string', 'max:16384'],
        ]);

        $tweet = $this->tweetService->create($validated['username'], $validated['text']);
        
        return response()->json($tweet,201);
    }

    public function destroy(Request $request, int $tweetId) {

        if ($this->tweetService->destroy($tweetId)) {
            return response()->json(null, 200);
        }

        throw new HttpException(500, "Server cannot delete user");
    }

    public function getUserLatestTweets(Request $request, string $username) {
        return response()->json($this->tweetService->getUserLatestTweets($username), 200);
    }
}

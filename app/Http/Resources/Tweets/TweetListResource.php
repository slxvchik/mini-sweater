<?php

namespace App\Http\Resources\Tweets;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'created_at' => $this->created_at,
            // 'user' => [
            //     'id' => $this->user->id,
            //     'username' => $this->user->username,
            // ],
            // todo: 'likes'
        ];
    }
}

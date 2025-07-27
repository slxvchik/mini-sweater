<?php

namespace App\Http\Resources\Comments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentListResource extends JsonResource
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
            'updated_at' => $this->updated_at,
            'parent_id' => $this->parent_id,
            'likes_count' => $this->likes_count,
            'is_liked' => $this->is_liked,
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
            ]
        ];
    }
}

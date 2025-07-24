<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Comment extends Model
{
    public $timestamps = false;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}

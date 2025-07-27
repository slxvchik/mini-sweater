<?php

namespace App\Models;

use App\HasLikes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;

class Tweet extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'text',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'created_at' => 'datetime',
            // 'updated_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class);
    }
}

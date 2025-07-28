<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public $timestamps = false;
    public $fillable = ['follower_id', 'followed_id', 'created_at'];
}

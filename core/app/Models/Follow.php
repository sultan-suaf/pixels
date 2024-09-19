<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public function followerProfile()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function followingProfile()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}

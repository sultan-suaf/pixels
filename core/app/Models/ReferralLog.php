<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralLog extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }
}

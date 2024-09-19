<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = ['id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributor()
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function imageFile()
    {
        return $this->belongsTo(ImageFile::class);
    }
}

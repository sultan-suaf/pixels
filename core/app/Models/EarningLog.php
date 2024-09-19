<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarningLog extends Model
{
    public function contributor()
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function imageFile(){
        return $this->belongsTo(ImageFile::class,'image_file_id');
    }
}

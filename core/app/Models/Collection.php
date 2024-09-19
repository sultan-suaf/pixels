<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use GlobalStatus;

    public function collectionImage()
    {
        return $this->hasMany(CollectionImage::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'collection_images')->where('status', Status::ENABLE);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', 1);
    }
}

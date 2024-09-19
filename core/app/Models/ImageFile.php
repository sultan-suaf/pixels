<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ImageFile extends Model
{
    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
    function scopeActive($q)
    {
        $q->where('status', Status::ENABLE);
    }
    function scopeInactive($q)
    {
        $q->where('status', Status::DISABLE);
    }
    function scopePremium($q)
    {
        $q->where('is_free', Status::PREMIUM);
    }
    function scopeFree($q)
    {
        $q->where('is_free', Status::FREE);
    }
}

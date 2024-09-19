<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionImage extends Model
{
    protected $guarded = ['id'];

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}

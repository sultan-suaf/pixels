<?php

namespace App\Models;


use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use GlobalStatus;

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}

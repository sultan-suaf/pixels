<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    use GlobalStatus;

    protected $casts = [
        'supported_file_extension' => 'array'
    ];

    public function image()
    {
        return $this->hasMany(Image::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'images', 'file_type_id', 'category_id')
            ->where('images.status', Status::ENABLE)
            ->withCount('images as category_image')
            ->orderBy('category_image', 'DESC')
            ->distinct();
    }

    public function scopeApprovedImageCount($query)
    {
        $query->withCount([
            'image' => function ($image) {
                $image->approved();
            },
        ]);
    }
}

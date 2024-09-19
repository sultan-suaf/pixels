<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Image extends Model
{
  

    protected $casts = [
        'tags'        => 'array',
        'extensions'  => 'array',
        'colors'      => 'array',
    ];

    protected $append = 'is_free';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function files()
    {
        return $this->hasMany(ImageFile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    
    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }



    public function reviewer()
    {
        return $this->belongsTo(Reviewer::class);
    }
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_images', 'image_id', 'collection_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function getIsFreeAttribute()
    {
        return $this->isFree();
    }

    public function isFree()
    {
        $isFree = true;
        if ($this->files()->where('is_free', Status::NO)->first()) {
            $isFree = false;
        }

        return $isFree;
    }

    //scope
    public function scopeHasActiveFiles($query)
    {
        $query->whereHas('files', function ($file) {
            $file->active();
        });
    }
    public function scopePending($query)
    {
        $query->where('status', Status::IMAGE_PENDING);
    }

    public function scopeApproved($query)
    {
        $query->where('status', Status::IMAGE_APPROVED);
    }


    public function scopeRejected($query)
    {
        $query->where('status', Status::IMAGE_REJECTED);
    }

    public function scopePremium($query)
    {
        $query->whereHas('files', function ($q) {
            $q->premium()->active();
        });
    }

    public function scopeFeatured($query)
    {
        $query->where('is_featured', Status::YES);
    }

    public function scopePopular($query)
    {
        $query->where('total_view', '>', 0)->orderBy('total_view', 'DESC');
    }

    public function scopeMostDownload($query)
    {
        $query
            ->withSum(
                ['files as total_downloads'],
                'total_downloads'
            )->having('total_downloads', '>', 0)
            ->orderBy('total_downloads', 'DESC');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->status == Status::IMAGE_APPROVED) {
                    $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
                } elseif ($this->status == Status::IMAGE_REJECTED) {
                    $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
                } else {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                }
                return $html;
            }
        );
    }
}

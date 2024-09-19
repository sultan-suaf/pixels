<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Reviewer extends Authenticatable
{
    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'address' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';
        if ($this->status == 1) {
            $html = '<span><span class="badge badge--success">' . trans('Active') . '</span>';
        } else {
            $html = '<span><span class="badge badge--danger">' . trans('Banned') . '</span>';
        }

        return $html;
    }
}

<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $casts = [

        'sender' => 'object',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function deposit()
    {
    
        return $this->hasOne(Deposit::class);
    }




    public function scopePending()
    {
        return $this->where('status', Status::DONATION_PENDING);
    }

    public function scopePaid()
    {
        return $this->where('status', Status::DONATION_PAID);
    }
    public function scopeRejected()
    {
        return $this->where('status', Status::DONATION_REJECT);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->status == Status::DONATION_PENDING) {
                    $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
                } elseif ($this->status == Status::DONATION_PAID) {
                    $html = '<span class="badge badge--success">' . trans('Paid') . '</span>';
                } elseif ($this->status == Status::DONATION_REJECT) {
                    $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
                } 
                return $html;
            }
        );
    }
}

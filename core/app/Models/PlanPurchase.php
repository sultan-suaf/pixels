<?php

namespace App\Models;

use App\Constants\Status;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PlanPurchase extends Model
{
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'trx', 'trx');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '<span class="badge badge--';
        if ($this->status == Status::PURCHASE_SUCCESS && $this->expired_at > Carbon::now()) {
            $class = 'success';
            $text = trans('Purchased');
        } elseif ($this->status == Status::PURCHASE_PENDING) {
            $class = 'warning';
            $text = trans('Pending');
        } elseif ($this->status == Status::PURCHASE_REJECT) {
            $class = 'danger';
            $text = trans('Rejected');
        } elseif ($this->status == Status::PURCHASE_EXPIRED || $this->expired_at <= Carbon::now()) {
            $class = 'danger';
            $text = trans('Expired');
        } else {
            $class = 'dark';
            $text = trans('Initiated');
        }
        $html .= $class . '">' . $text . '</span>';
        return $html;
    }
}

<?php

namespace App\Models;

use App\Traits\GlobalStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Plan extends Model
{
    use GlobalStatus;

    protected function dailyLimitText(): Attribute
    {
        return new Attribute(
            get: fn () => $this->daily_limit < 0 ? trans('Unlimited') : $this->daily_limit,
        );
    }

    protected function monthlyLimitText(): Attribute
    {
        return new Attribute(
            get: fn () => $this->monthly_limit < 0 ? trans('Unlimited') : $this->monthly_limit,
        );
    }
}

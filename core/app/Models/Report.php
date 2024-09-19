<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    

    public function scopePending($query)
    {
        $query->where('status', Status::DISABLE);
    }
}

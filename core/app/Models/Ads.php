<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ads extends Model
{
 


    public function getTypeTextAttribute()
    {
        $type = 'Image';
        if ($this->type == 1) {
            $type = 'Script';
        }
        return $type;
    }
}

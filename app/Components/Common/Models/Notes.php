<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{

    protected $fillable = ['body'];
    public function noteable()
    {
        return $this->morphTo();
    }
}

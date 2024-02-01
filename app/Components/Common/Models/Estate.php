<?php

namespace  App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    
    public $timestamps = false;

    public function townships()
    {
        return $this->hasMany(Township::class);
    }
}

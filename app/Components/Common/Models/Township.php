<?php

namespace  App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\Tracking\Models\Prospect;

class Township extends Model
{
    public $timestamps = false;

    protected $with = ['estate'];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

}

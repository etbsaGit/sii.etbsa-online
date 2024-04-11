<?php

namespace App\Components\Common\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{

    protected $fillable = ['body', 'created_by'];
    public function noteable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id','created_by');
    }
}

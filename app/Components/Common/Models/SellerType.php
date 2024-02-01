<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\User\Models\User;

class SellerType extends Model
{
    protected $table = 'departments';

    protected $fillable = [];

    public function users()
    {
        return $this->belongsToMany(User::class,'sellers_type_pivot','seller_type_id');
    }
}

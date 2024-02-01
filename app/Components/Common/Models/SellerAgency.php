<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\User\Models\User;

class SellerAgency extends Model
{
    protected $table = 'agencies';

    protected $fillable = [];

    public function users()
    {
        return $this->belongsToMany(User::class,'sellers_agency_pivot','agency_id');
    }
}

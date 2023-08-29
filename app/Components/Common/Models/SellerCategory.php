<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\User\Models\User;

class SellerCategory extends Model
{
    protected $table = 'cat_product_category';

    protected $fillable = [];

    public function users()
    {
        return $this->belongsToMany(User::class,'seller_category_pivot','category_id');
    }
}

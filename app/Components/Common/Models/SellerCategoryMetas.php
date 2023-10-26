<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;
use App\Components\User\Models\User;

class SellerCategoryMetas extends Model
{
    protected $table = 'cat_product_category';

    protected $fillable = [];

    public function users()
    {
        return $this->belongsToMany(User::class,'seller_category_metas_pivot','category_id');
    }
}

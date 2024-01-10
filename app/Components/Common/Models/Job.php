<?php

namespace App\Components\Common\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];

    public function users()
    {
        return $this->hasMany(User::class);
    }



    // public function purchaseOrder()
    // {
    //     return $this->belongsToMany(PurchaseOrder::class, 'purchase_pivot_charges');
    // }
    // public function purchaseOrder()
    // {
    //     return $this->belongsToMany(PurchaseOrder::class, 'purchase_agency_pivot_table', 'department_id');
    // }
}

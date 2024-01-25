<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyBankAccount extends Model
{
       /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agency_bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['agency_id', 'bank_name', 'account_number', 'balance'];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
}

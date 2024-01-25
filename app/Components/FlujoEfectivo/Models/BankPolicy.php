<?php

namespace App\Components\FlujoEfectivo\Models;

use App\Components\Common\Models\AgencyBankAccount;
use App\Components\Common\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class BankPolicy extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bank_policies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_bank_accounts_id',
        'currency_id',
        'description',
        'date_apply',
        'policy_type',
        'payment_source',
        'reference',
        'exchange_rate',
        'amount'
    ];

    public function agencyBankAccount()
    {
        return $this->belongsTo(AgencyBankAccount::class, 'agency_bank_accounts_id');
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }


}

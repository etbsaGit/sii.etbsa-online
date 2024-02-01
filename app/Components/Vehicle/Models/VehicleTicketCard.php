<?php

namespace App\Components\Vehicle\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleTicketCard extends Model
{
    protected $table = 'vehicle_ticket_cards';
    protected $primaryKey = 'ticket_card';
    protected $fillable = ['ticket_card', 'account_balance'];
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'ticket_card', 'ticket_card');
    }

    // Search
    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('ticket_card', 'like', "%{$search}%");
            });
        });
    }
}

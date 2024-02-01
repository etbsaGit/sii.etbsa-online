<?php

namespace App\Components\RRHH\Models;

class DirectBoss extends Employee
{
    public function direct_boss()
    {
        return $this->hasMany(Employee::class, 'direct_boss');
    }
}

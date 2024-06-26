<?php

namespace App\Components\EquipDB\Models;

use Illuminate\Database\Eloquent\Model;


class CarteraClientes extends Model
{
    protected $connection = 'sqlite_equip_db';
    protected $table = 'cartera_clientes';

    public $incrementing = false;
    public $timestamps = false;

    protected $cast = [

    ];



    public function scopeSearch($query, string $search)
    {
        // $query->when($search ?? null, function ($query, $search) {
        //     $query->where(function ($query) use ($search) {
        //         $query->orWhere('c_ClaveProdServ', 'like', "{$search}%");
        //         $query->orWhere('Descripción', 'like', "%{$search}%");
        //         $query->orWhere('Palabrassimilares', 'like', "%{$search}%");
        //     });
        // });
    }
    public function scopeFilter($query, array $filters)
    {
        // $query->when($filters['claveProdServ'] ?? null, function ($query, $claveProdServ) {
        //     $query->where(function ($query) use ($claveProdServ) {
        //         $query->orWhere('c_ClaveProdServ', 'like', '%' . $claveProdServ . '%');
        //     });
        // })->when($filters['descripcion'] ?? null, function ($query, $descripcion) {
        //     $query->where(function ($query) use ($descripcion) {
        //         $query->orWhere('Descripción', 'like', '%' . $descripcion . '%');
        //     });
        // })->when($filters['PalabraSimilares'] ?? null, function ($query, $PalabraSimilares) {
        //     $query->where(function ($query) use ($PalabraSimilares) {
        //         $query->orWhere('Palabrassimilares', 'like', '%' . $PalabraSimilares . '%');
        //     });
        // });
    }


}
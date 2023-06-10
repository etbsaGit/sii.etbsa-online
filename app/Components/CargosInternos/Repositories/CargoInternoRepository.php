<?php

namespace App\Components\CargosInternos\Repositories;

use App\Components\CargosInternos\Models\CargosInternos;
use App\Components\Core\BaseRepository;

class CargoInternoRepository extends BaseRepository
{
    public function __construct(CargosInternos $model)
    {
        parent::__construct($model);
    }

    public function list($params)
    {
        return $this->get($params, [], function ($q) use ($params) {
            $q->search($params['search'] ?? '');
            return $q;
        });
    }

    public function updateStatusCargoInterno($estatus, $cargos_internos = [])
    {
        
    }
}
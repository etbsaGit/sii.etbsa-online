<?php

namespace App\Imports;

use App\GpsChips;
use Maatwebsite\Excel\Concerns\ToModel;

class GpsChipsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new GpsChips([
            //
        ]);
    }
}

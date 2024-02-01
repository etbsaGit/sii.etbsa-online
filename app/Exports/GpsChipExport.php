<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class GpsChipExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $chip;

    public function __construct($chip = null)
    {
        $this->chip = $chip;
    }

    public function view(): View
    {
        return view('exports.gps_chips', [
            'chip' => $this->chip
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'CHIPS';
    }
}

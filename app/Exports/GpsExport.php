<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class GpsExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $gps;

    public function __construct($gps = null)
    {
        $this->gps = $gps;
    }

    public function view(): View
    {
        return view('exports.gps', [
            'gps' => $this->gps
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'GPS';
    }
}

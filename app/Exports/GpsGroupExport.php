<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class GpsGroupExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $group;

    public function __construct($group = null)
    {
        $this->group = $group;
    }

    public function view(): View
    {
        return view('exports.gps_groups', [
            'group' => $this->group
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'groupS';
    }
}

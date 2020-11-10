<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class TrackingExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $tracking;

    public function __construct($tracking = null)
    {
        $this->tracking = $tracking;
    }

    public function view(): View
    {
        return view('exports.tracking', [
            'tracking' => $this->tracking
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'tracking';
    }
}

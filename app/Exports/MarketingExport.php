<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class MarketingExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $history_sale;

    public function __construct($history_sale = null)
    {
        $this->history_sale = $history_sale;
    }

    public function view(): View
    {
        return view('exports.history_sale', [
            'history_sale' => $this->history_sale
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Historial_Ventas';
    }
}

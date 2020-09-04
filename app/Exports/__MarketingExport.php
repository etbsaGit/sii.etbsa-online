<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class __MarketingExport implements FromCollection, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $history_sale = [];

    public function __construct($history_sale = null)
    {
        $this->history_sale = $history_sale;
    }

    public function collection()
    {
        return collect($this->history_sale);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Historial_Ventas';
    }
}

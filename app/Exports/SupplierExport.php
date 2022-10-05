<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupplierExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $supplier;

    public function __construct($supplier = null)
    {
        $this->supplier = $supplier;
    }

    public function view(): View
    {
        return view('exports.supplier', [
            'supplier' => $this->supplier
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'supplier';
    }
}

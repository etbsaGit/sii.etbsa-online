<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseInvoiceExport implements FromView, ShouldAutoSize, WithTitle, WithStyles
{

    use Exportable;
    protected $purchase_invoice;

    public function __construct($purchase_invoice = null)
    {
        $this->purchase_invoice = $purchase_invoice;
    }

    public function view(): View
    {
        return view('exports.purchase_invoice', [
            // 'group' => $this->group->filter(function ($item) {
            //     return $item->gps_count > 0;
            // })->values()
            "invoice" => $this->purchase_invoice
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'FacturasXPagar';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);
    }
}

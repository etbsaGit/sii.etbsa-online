<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductExport implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    protected $products;

    public function __construct($products = null)
    {
        $this->products = $products;
    }

    public function view(): View
    {
        return view('exports.products', [
            // 'group' => $this->group->filter(function ($item) {
            //     return $item->gps_count > 0;
            // })->values()
            "products" => $this->products
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Lista Productos';
    }
}

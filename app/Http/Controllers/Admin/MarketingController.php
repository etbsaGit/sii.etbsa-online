<?php

namespace App\Http\Controllers\Admin;

use App\Components\Marketing\Repositories\MarketingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends AdminController
{
    /**
     * @var MarketingRepository
     */
    private $marketingRepository;

    /**
     * FileGroupController constructor.
     * @param MarketingRepository $marketingRepository
     */
    public function __construct(MarketingRepository $marketingRepository)
    {
        $this->marketingRepository = $marketingRepository;
    }

    public function salesHistory(Request $request)
    {
        $data = $this->marketingRepository->index($request->all());

        return $this->sendResponseOk($data);
    }

    public function resources()
    {
        $months = DB::table('marketing_import')->distinct()->get(['MES']);
        $years = DB::table('marketing_import')->distinct()->get(['ANIO']);
        $agencies = DB::table('marketing_import')->distinct()->get(['SUCURSAL']);
        $type_of_sale = DB::table('marketing_import')->distinct()->get(['TIPO_DE_VENTA']);
        $brands = DB::table('marketing_import')->distinct()->get(['MARCA']);
        $seller = DB::table('marketing_import')->distinct()->get(['VENDEDOR']);
        return $this->sendResponseOk(compact('months', 'years', 'agencies', 'type_of_sale', 'brands','seller'));
    }
}

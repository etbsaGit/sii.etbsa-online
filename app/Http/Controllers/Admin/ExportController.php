<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Components\Gps\Repositories\GpsRepository;
use App\Components\Marketing\Repositories\MarketingRepository;
use App\Exports\GpsExport;
use App\Exports\MarketingExport;

class ExportController extends AdminController
{
    /**
     * @var GpsRepository
     */
    private $gpsRepository;
    private $marketingRepository;

    /**
     * FileGroupController constructor.
     * @param GpsRepository $gpsRepository
     */
    public function __construct(GpsRepository $gpsRepository,MarketingRepository $marketingRepository)
    {
        $this->gpsRepository = $gpsRepository;
        $this->marketingRepository = $marketingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportGps(Request $request)
    {
        $data = $this->gpsRepository->index($request->all());

        return Excel::download(new GpsExport($data), 'gps.xlsx');
    }

    public function exportMarketing(Request $request)
    {
        $data = $this->marketingRepository->index($request->all());

        return Excel::download(new MarketingExport($data), 'maketing.xlsx');
    }
}

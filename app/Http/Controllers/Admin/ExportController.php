<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Components\Gps\Repositories\GpsRepository;
use App\Exports\GpsExport;

class ExportController extends AdminController
{
    /**
     * @var GpsRepository
     */
    private $gpsRepository;

    /**
     * FileGroupController constructor.
     * @param GpsRepository $gpsRepository
     */
    public function __construct(GpsRepository $gpsRepository)
    {
        $this->gpsRepository = $gpsRepository;
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
}

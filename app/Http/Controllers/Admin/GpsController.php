<?php

namespace App\Http\Controllers\Admin;

use App\Components\Gps\Repositories\GpsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsController extends AdminController
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
    public function index(Request $request)
    {
        $data = $this->gpsRepository->index($request->all());

        return $this->sendResponseOk($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(), [
            'name' => 'required|unique:gps,name',
            'gps_chip_id' => 'required|unique:gps,gps_chip_id',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
            'name.unique' => 'Nombre GPS Duplicado',
            'gps_chip_id.unique' => 'Error Chip Duplicado, Ya se encuentra Asignado',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $gps = $this->gpsRepository->create($request->all());

        if (!$gps) {
            return $this->sendResponseBadRequest("Failed to create.");
        }

        return $this->sendResponseCreated($gps);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gps = $this->gpsRepository->find($id, ['chip', 'gpsGroup', 'historical', 'user']);

        if (!$gps) {
            return $this->sendResponseNotFound();
        }

        return $this->sendResponseOk($gps);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = validator($request->all(), [
            'name' => 'required|string',
            'amount' => 'numeric',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $this->gpsRepository->update($id, $request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed to update");
        }

        return $this->sendResponseOk([], "Updated.");
    }

    public function renewInvoice(Request $request, $id)
    {
        $validate = validator($request->all(), [
            'invoice' => 'required',
            'amount' => 'required|numeric',
            'currency' => 'required',
            'exchange_rate' => 'required|numeric',
        ], [
            'invoice.required' => 'La Factura es Requerida',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = false;
        DB::transaction(function () use ($id, $request, $updated) {
            $this->gpsRepository->keepHistorical($id);
            $renew = new Carbon($request->renew_date);
            $renew->setYear(Carbon::now()->year);

            $request['renew_date'] = $renew->addYear();
            $request['estatus'] = 'RENOVADO';
            $request['uploaded_by'] = auth()->user()->id;
            $updated = $this->gpsRepository->update($id, $request->all());
        });

        // if (!$updated) {
        //     return $this->sendResponseBadRequest("Error en la Renovacion ");
        // }

        return $this->sendResponseOk([], "GPS RENOVADO.");

    }

    public function cancelled(Request $request, $id)
    {
        $validate = validator($request->all(), [
            'cancellation_date' => 'required',
            'description' => 'required',
        ], [
            'cancellation_date.required' => 'Ingrese Fecha de Cancelacion',
            'description.required' => 'Ingrese Motivo de la Cancelacion',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = false;
        DB::transaction(function () use ($id, $request, $updated) {
            $this->gpsRepository->keepHistorical($id);
            $this->gpsRepository->cancelGps($id);
            $request['estatus'] = 'CANCELADO';
            $request['invoice'] = null;
            $request['amount'] = 0;
            $request['currency'] = 'MXN';
            $request['exchange_rate'] = 1;
            $request['renew_date'] = null;
            // $request['installation_date'] = null;
            $request['uploaded_by'] = auth()->user()->id;
            $updated = $this->gpsRepository->update($id, $request->all());
        });
        // if (!$updated) {
        //     return $this->sendResponseBadRequest("Error en la Cancelacion");
        // }

        return $this->sendResponseOk([], "GPS Cancelado.");
    }

    public function reasign(Request $request, $id)
    {
        $validate = validator($request->all(), [
            'name' => 'required',
            'gps_chip_id' => 'required',
            'gps_group_id' => 'required',
            'installation_date' => 'required',
            // 'gps_chip_id' => 'required|unique:gps,gps_chip_id',
        ], [
            'name.required' => 'Nombre GPS es Requrido',
            // 'gps_chip_id.unique' => 'Error Chip Duplicado, Ya se encuentra Asignado',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = false;
        DB::transaction(function () use ($id, $request, $updated) {
            $this->gpsRepository->keepHistorical($id);
            $renew = new Carbon($request->installation_date);
            $renew->setYear(Carbon::now()->year);

            // $request['renew_date'] = $renew;
            $request['estatus'] = 'REASIGNADO';
            $request['cancellation_date'] = null;
            $request['uploaded_by'] = auth()->user()->id;
            $updated = $this->gpsRepository->update($id, $request->all());
        });

        // if (!$updated) {
        //     return $this->sendResponseBadRequest("Error en Reasignar");
        // }

        return $this->sendResponseOk([], "GPS Reasignado.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->gpsRepository->delete($id);

        return $this->sendResponseOk([], "Deleted.");
    }

    public function stats(Request $request)
    {
        $year = Carbon::now()->year;
        $stats = [];
        $list;

        for ($month = 1; $month <= 12; $month++) {
            $params = [
                'month' => $month,
                'year' => $year,
                // 'month_installation' => $month,
                // 'year_installation' => $month,
                'paginate' => 'no',
            ];
             $stats[] = $this->gpsRepository->stats($params);
        }

        return $this->sendResponseOk($stats, "Get Estadisticas GPS.");

    }

}

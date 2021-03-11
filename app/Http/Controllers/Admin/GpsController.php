<?php

namespace App\Http\Controllers\Admin;

use App\Components\Gps\Models\Gps;
use App\Components\Gps\Models\GpsChips;
use App\Components\Gps\Models\GpsGroup;
use App\Components\Gps\Repositories\GpsRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GpsController extends AdminController
{
    /**
     * @var GpsRepository
     */
    private $gpsRepository;

    /**
     * GpsController constructor.
     * @param GpsRepository $gpsRepository
     */
    public function __construct(GpsRepository $gpsRepository)
    {
        $this->gpsRepository = $gpsRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $gps = $this->gpsRepository->index($request->all());


        return $this->sendResponseOk(compact('gps'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resources = [
            'groups_gps' => GpsGroup::all('id', 'name'),
            'chips_gps' => GpsChips::whereNull('gps_id')->get('sim', 'costo'),
            'types' => ['CONTADO', 'CREDITO', 'CARGO']
        ];

        return $this->sendResponseOk(compact('resources'));
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
            'gps_group_id' => 'required',
            'installation_date' => 'required',
            'renew_date' => 'required',
            'description' => 'required',
            'payment_type' => 'required',
            'gps_chip_id' => 'required|unique:gps,gps_chip_id',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
            'name.unique' => 'Nombre GPS Duplicado',
            'gps_chip_id.unique' => 'Error Chip Duplicado, Ya se encuentra Asignado',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $request['uploaded_by'] = Auth::user()->id;
        $gps = $this->gpsRepository->create($request->all());

        if (!$gps) {
            return $this->sendResponseBadRequest("Failed to create.");
        }

        return $this->sendResponseCreated($gps);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Gps  $gps
     * @return \Illuminate\Http\Response
     */
    public function show(Gps $gp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Gps  $gps
     * @return \Illuminate\Http\Response
     */
    public function edit(Gps $gp)
    {
        $data = [
            'name' => $gp->name,
            'description' => $gp->description,
            'installation_date' => $gp->installation_date,
            'renew_date' => $gp->renew_date,
            'currency' => $gp->currency,
            'invoice' => $gp->invoice,
            'invoice_date' => $gp->invoice_date,
            'amount' => $gp->amount,
            'exchange_rate' => $gp->exchange_rate,
            'payment_type' => $gp->payment_type,
            'gps_group_id' => $gp->gps_group_id,
            'gps_chip_id' => $gp->chip->sim ?? null,
            'historical' => $gp->historical
        ];
        return $this->sendResponseOk($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gps  $gps
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gps $gp)
    {

        $validate = validator($request->all(), [
            'name' => ['required', Rule::unique('gps')->ignore($gp->id)],
            'gps_group_id' => 'required',
            'installation_date' => 'required',
            'renew_date' => 'required',
            'description' => 'required',
            'payment_type' => 'required',
            'gps_chip_id' => ['required', Rule::unique('gps')->ignore($gp->id)],
            'invoice_date' => [Rule::requiredIf($request->invoice)],
            'amount' => [Rule::requiredIf($request->invoice)],
            'exchange_rate' => [Rule::requiredIf($request->invoice)],
        ], [
            'name.unique' => 'Nombre GPS Duplicado',
            'gps_chip_id.unique' => 'Chip Duplicado,El Chip ya se encuentra Asignado',
            'invoice_date.required' => 'Fecha Facturacion Requerida',
            'amount.required' => 'Monto Factura Requerido',
            'exchange_rae.required' => 'Tipo de Campio Requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $gp->update($request->all());

        if (!$updated) {
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }

        return $this->sendResponseUpdated();
    }

    public function invoice(Request $request, Gps $gps)
    {
        $validate = validator($request->all(), [
            'invoice' => 'required',
            'invoice_date' => [Rule::requiredIf($request->invoice)],
            'amount' => [Rule::requiredIf($request->invoice)],
            'exchange_rate' => [Rule::requiredIf($request->invoice)],
        ], [
            'invoice.required' => 'Folio Factura Requerido',
            'invoice_date.required' => 'Fecha Facturacion Requerida',
            'amount.required' => 'Monto Factura Requerido',
            'exchange_rae.required' => 'Tipo de Campio Requerido',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        DB::beginTransaction();
        try {
            $renew = new Carbon($request->invoice_date);
            $request['renew_date'] = $renew->setYear(Carbon::now()->year)->addYear();

            $this->gpsRepository->keepHistorical($gps);
            $gps->update($request->all());
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        DB::commit();
        return $this->sendResponseUpdated([], 'GPS Facturado');
    }

    public function cancel(Request $request, Gps $gps)
    {
        $validate = validator($request->all(), [
            'cancellation_date' => 'required',
            'description' => 'required',

        ], [
            'cancellation_date.required' => 'Fecha de Cnacelacion Requrida',
            'description.required' => 'Motivo de Cancelacion Rquerida',

        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        DB::beginTransaction();
        try {
            $request['gps_chip_id'] = null;
            $request['renew_date'] = null;
            $gps->update($request->all());
        } catch (Exception $e) {
            DB::rollback();
            return $this->sendResponseBadRequest("Error en la Actualizacion");
        }
        DB::commit();
        return $this->sendResponseUpdated([], 'GPS Cancelado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gps  $gps
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gps $gp)
    {
        //
    }

    public function stats()
    {
        $year  = Carbon::now()->year;
        $stats = [];

        for ($month = 1; $month <= 12; $month++) {
            $stats[] = $this->gpsRepository->getStatsGps($month, $year);
        }

        return $this->sendResponseOk($stats, "Get Estadisticas GPS.");
    }

    public function resources()
    {
        $chips = GpsChips::all('sim');
        $groups = GpsGroup::all('id', 'name');
        $types = ['CONTADO', 'CREDITO', 'CARGO'];
        return $this->sendResponseOk(compact('chips', 'groups', 'types'));
    }
}

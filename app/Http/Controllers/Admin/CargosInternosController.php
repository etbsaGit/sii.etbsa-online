<?php

namespace App\Http\Controllers\Admin;


use App\Components\CargosInternos\Models\CargosInternos;
use App\Components\CargosInternos\Repositories\CargoInternoRepository;
use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Estatus;
use App\Events\CargoInternoEstatusChanged;
use App\Http\Requests\CargosInternosRequests\StoreCargoInterno;
use App\Http\Requests\CargosInternosRequests\UpdateCargoInterno;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class CargosInternosController extends AdminController
{


    private $cargoInternoRepository;

    public function __construct(CargoInternoRepository $cargoInternoRepository)
    {
        $this->cargoInternoRepository = $cargoInternoRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->cargoInternoRepository->list(request()->all());
        $data->setCollection(
            $data->getCollection()->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'num_economico' => $model->num_economico,
                    'num_cliente' => $model->num_cliente,
                    'nip' => $model->nip,
                    'concepto_cargo' => $model->concepto_cargo,
                    'ot' => $model->ot,
                    'amount' => $model->amount,
                    'created_by' => $model->createdBy->name,
                    'estatus' => $model->estatus->title,
                    'sucursales' => $model->sucursales,
                    // 'cargos_sucursal' => $model->cargosSucursal->map->only(['sucursal_id', 'department_id']),
                    'cargos_sucursal' => $model->cargosSucursal->map(
                        function ($item) {
                                return [
                                    'agencia' => Agency::find($item->sucursal_id)->title,
                                    'departamento' => Department::find($item->department_id)->title,
                                    'percent' => $item->percent
                                ];
                            }
                    )
                ];
            })
        )->toArray();
        return $this->sendResponseOk($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $options = $this->getOptions();

        return $this->sendResponseOk(compact('options'), "Create CI");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCargoInterno $request)
    {
        return DB::transaction(
            function () use ($request) {
                // $this->cargoInternoRepository->create($request->all());
                tap(
                    CargosInternos::withoutEvents(function () use ($request) {
                        return CargosInternos::create($request->all());
                    }),
                    function (CargosInternos $cargo_interno) use ($request) {
                        $cargo_interno->createdBy()->associate(auth()->user());
                        $cargo_interno->estatus()->associate(
                            Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first()
                        );

                        foreach ($request->get('cargos_sucursal', []) as $item) {
                            $cargo_interno->sucursales()->attach($item['sucursal_id'], [
                                'sucursal_id' => $item['sucursal_id'],
                                'department_id' => $item['department_id'],
                                'percent' => $item['percent'],
                            ]);
                        }
                        $cargo_interno->save();

                        return $this->sendResponseCreated([$cargo_interno->fresh()]);
                    }
                );
            }
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return \Illuminate\Http\Response
     */
    public function show(CargosInternos $cargosInternos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return \Illuminate\Http\Response
     */
    public function edit(CargosInternos $cargos_interno)
    {
        // $options = $this->getOptions();
        $item = collect($cargos_interno)->union(
            [
                'estatus' => $cargos_interno->estatus->title,

                'cargos_sucursal' => $cargos_interno->cargosSucursal->map(
                    function ($item) {
                        return [
                            'sucursal_id' => $item->sucursal_id,
                            'department_id' => $item->department_id,
                            'percent' => $item->percent
                        ];
                    }
                )
            ]
        )->only(
                'description',
                'num_economico',
                'num_cliente',
                'nip',
                'concepto_cargo',
                'ot',
                'mano_obra',
                'refacciones',
                'kilometraje',
                'foraneo',
                'amount',
                'estatus',
                'cargos_sucursal'
            );

        return $this->sendResponseOk(compact('item'), "Edit CI");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargosInternos
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCargoInterno $request, CargosInternos $cargos_interno)
    {
        return DB::transaction(
            function () use ($request, $cargos_interno) {
                CargosInternos::withoutEvents(function () use ($request, $cargos_interno) {
                    $cargos_interno->update($request->all());
                    $cargos_interno->createdBy()->associate(auth()->user());
                    $cargos_interno->estatus()->associate(
                        Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first()
                    );
                    $cargos_interno->sucursales()->detach();
                });

                foreach ($request->get('cargos_sucursal', []) as $item) {
                    $cargos_interno->sucursales()->attach($item['sucursal_id'], [
                        'sucursal_id' => $item['sucursal_id'],
                        'department_id' => $item['department_id'],
                        'percent' => $item['percent'],
                    ]);
                }
                $cargos_interno->save();
                return $this->sendResponseUpdated([$cargos_interno->fresh()]);

            }
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Components\CargosInternos\Models\CargosInternos  $cargos_interno
     * @return \Illuminate\Http\Response
     */
    public function destroy(CargosInternos $cargos_interno)
    {
        $cargos_interno->delete();
        $this->sendResponseDeleted();
    }

    public function updateStatus(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $status = null;
            if ($request->status_key == Estatus::ESTATUS_AUTORIZADO) {
                $status = Estatus::where('key', Estatus::ESTATUS_AUTORIZADO)->first();
                $update_request = [
                    'estatus_id' => $status->id,
                    'autorized_by' => auth()->user()->id,
                    'authorization_date' => Carbon::now()
                ];
            }
            if ($request->status_key == Estatus::ESTATUS_DENGAR) {
                $status = Estatus::where('key', Estatus::ESTATUS_DENGAR)->first();
                $update_request = [
                    'estatus_id' => $status->id,
                    'autorized_by' => null,
                    'authorization_date' => null,
                    'schedule_date' => null
                ];
            }
            if ($request->status_key == Estatus::ESTATUS_POR_PAGAR) {
                $status = Estatus::where('key', Estatus::ESTATUS_POR_PAGAR)->first();
                $update_request = [
                    'estatus_id' => $status->id,
                    'autorized_by' => auth()->user()->id,
                    'schedule_date' => Carbon::now()
                ];
            }

            $cargos_internos = CargosInternos::find($request->cargos_internos_ids)
                ->map->update($update_request);

            return $this->sendResponseUpdated($cargos_internos, "Estatus Actualizado");
        });
    }

    public function getOptions()
    {
        return [
            'agencies' => Agency::get(['id', 'title']),
            'departments' => Department::get(['id', 'title']),
        ];
    }
}
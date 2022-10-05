<?php

namespace App\Http\Controllers\Admin;

use App\components\NT\Models\AmsComparative;
use App\Components\NT\Repositories\AmsComparativeRepository;
use Auth;
use Illuminate\Http\Request;

class AmsComparativeController extends AdminController
{

    private $amsComparativeRepository;

    public function __construct(AmsComparativeRepository $amsComparativeRepository)
    {
        $this->amsComparativeRepository = $amsComparativeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->amsComparativeRepository->list(request()->all());
        return $this->sendResponseOk($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['created_by'] = Auth::user()->id;
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $ams_comparative = $this->amsComparativeRepository->create($request->all());
        if (!$ams_comparative) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        return $this->sendResponseCreated($ams_comparative);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NT\AmsComparative  $ams_comparative
     * @return \Illuminate\Http\Response
     */
    public function show(AmsComparative $ams_comparative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NT\AmsComparative  $ams_comparative
     * @return \Illuminate\Http\Response
     */
    public function edit(AmsComparative $ams_comparative)
    {
        return $this->sendResponseOk($ams_comparative);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NT\AmsComparative  $ams_comparative
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AmsComparative $ams_comparative)
    {
        $request['updated_by'] = Auth::user()->id;
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $ams_comparative->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update.");
        }
        return $this->sendResponseUpdated($updated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NT\AmsComparative  $ams_comparative
     * @return \Illuminate\Http\Response
     */
    public function destroy(AmsComparative $ams_comparative)
    {
        $ams_comparative->delete();
        return $this->sendResponseDeleted();
    }

    public function preview(Request $request)
    {
        $data = $request->all();
        $EQUIPMENT_TYPE_ARADO = "Arado";
        $EQUIPMENT_TYPE_RASTRA = "Rastra";
        $EQUIPMENT_TYPE_CULTIVADORA = "Cultivadora";
        $EQUIPMENT_TYPE_NIVELADORA = "Niveladora";
        $EQUIPMENT_TYPE_SEMBRADORA = "Sembradora";
        $EQUIPMENT_TYPE_FUMIGADORA = "Fumigadora";

        $table_without_ams = [];
        $table_with_ams = [];
        $table_diff = [];
        $table_save = [];

        $equipments_to_compare = $data['equipments'];

        foreach ($equipments_to_compare as $key => $equip) {
            $result_without_ams = [];
            $result_with_ams = [];
            $result_diff = [];

            if ($equip['category'] == $EQUIPMENT_TYPE_ARADO) {
                $result_without_ams = $this->ResultAradoWithoutAms($equip, $data);
                $result_with_ams = $this->ResultAradoWithAms($equip, $data);
            }
            if ($equip['category'] == $EQUIPMENT_TYPE_RASTRA) {
                $result_without_ams = $this->ResultRastraWithoutAms($equip, $data);
                $result_with_ams = $this->ResultRastraWithAms($equip, $data);
            }
            if ($equip['category'] == $EQUIPMENT_TYPE_CULTIVADORA) {
                $result_without_ams = $this->ResultCultivadoraWithoutAms($equip, $data);
                $result_with_ams = $this->ResultCultivadoraWithAms($equip, $data);
            }
            if ($equip['category'] == $EQUIPMENT_TYPE_NIVELADORA) {
                $result_without_ams = $this->ResultNiveldadoraWithoutAms($equip, $data);
                $result_with_ams = $this->ResultNiveladoraWithAms($equip, $data);
            }
            if ($equip['category'] == $EQUIPMENT_TYPE_SEMBRADORA) {
                $result_without_ams = $this->ResultSembradoraWithoutAms($equip, $data);
                $result_with_ams = $this->ResultSembradoraWithAms($equip, $data);
            }
            if ($equip['category'] == $EQUIPMENT_TYPE_FUMIGADORA) {
                $result_without_ams = $this->ResultFumigadoraWithoutAms($equip, $data);
                $result_with_ams = $this->ResultFumigadoraWithAms($equip, $data);
            }
            $table_without_ams[] = $result_without_ams;
            $table_with_ams[] = $result_with_ams;
            $table_diff[] = $this->ResultDifferenceAms($result_without_ams, $result_with_ams);
        }

        $table_save = $this->ResultSaveAms($table_diff, $data);
        return $this->sendResponseOk(compact(
            'table_without_ams',
            'table_with_ams',
            'table_diff',
            'table_save'
        ));
    }

    public function print(AmsComparative $ams_comparative)
    {
        $pdf = \PDF::loadView('pdf.ams_comparative', compact(
            'data',
            'table_without_ams',
            'table_with_ams',
            'table_diff',
            'table_save'
        ));
        return $pdf->stream();
    }

    public function ResultAradoWithoutAms($equip = [],  $data = [])
    {
        $anchoCorte = round((($equip['value'] * .25) * .8), 2);
        $pasadas = $equip['steps'];
        $haXdia = ($anchoCorte * $pasadas) * .8;
        $dieselXDia = $data['diesel_prepare'];
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = $data['tractor_potencia'] * 0.3;
        $llantasXHa = $data['tractor_potencia'] * 0.17;
        $seguroXHa = $data['tractor_potencia'] * 0.035;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultAradoWithAms($equip = [],  $data = [])
    {
        $anchoCorte = round((($equip['value'] * .25) * .8) * 1.25, 2);
        $pasadas = $equip['steps'] + 0.5;
        $haXdia = ($anchoCorte * $pasadas) * .9;
        $dieselXDia = $data['diesel_prepare'] * .9;
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = ($data['tractor_potencia'] * 0.3) * 0.88;
        $llantasXHa = $data['tractor_potencia'] * 0.17;
        $seguroXHa = $data['tractor_potencia'] * 0.035;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }

    public function ResultRastraWithoutAms($equip = [],  $data = [])
    {
        $anchoCorte = round(((($equip['value'] / 2) * 9) * 0.0254) * 0.8, 2);
        $pasadas = $equip['steps'];
        $haXdia = ($anchoCorte * $pasadas) * 0.6;
        $dieselXDia = $data['diesel_prepare'];
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = $data['tractor_potencia'] * 0.142;
        $llantasXHa = $data['tractor_potencia'] * 0.078;
        $seguroXHa = $data['tractor_potencia'] * 0.016;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultRastraWithAms($equip = [],  $data = [])
    {
        $anchoCorte = round((((($equip['value'] / 2) * 9) * 0.0254) * 0.8) * 1.25, 2);
        $pasadas = $equip['steps'] + 0.5;
        $haXdia = ($anchoCorte * $pasadas) * .7;
        $dieselXDia = $data['diesel_prepare'] * .9;
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = ($data['tractor_potencia'] * 0.142) * 0.88;
        $llantasXHa = $data['tractor_potencia'] * 0.078;
        $seguroXHa = $data['tractor_potencia'] * 0.016;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultCultivadoraWithoutAms($equip = [],  $data = [])
    {
        $anchoCorte = round(($equip['value'] * $data['grooves']) * 0.8, 2);
        $pasadas = $equip['steps'];
        $haXdia = ($anchoCorte * $pasadas) * 0.8;
        $dieselXDia = $data['diesel_work'];
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = $data['tractor_potencia'] * 0.145;
        $llantasXHa = $data['tractor_potencia'] * 0.08;
        $seguroXHa = $data['tractor_potencia'] * 0.017;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultCultivadoraWithAms($equip = [],  $data = [])
    {
        $anchoCorte = round((($equip['value'] * $data['grooves']) * 0.8) * 1.25, 2);
        $pasadas = $equip['steps'] + 0.5;
        $haXdia = ($anchoCorte * $pasadas) * 0.9;
        $dieselXDia = $data['diesel_work'] * 0.9;
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = ($data['tractor_potencia'] * 0.145) * 0.88;
        $llantasXHa = $data['tractor_potencia'] * 0.08;
        $seguroXHa = $data['tractor_potencia'] * 0.017;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultNiveldadoraWithoutAms($equip = [],  $data = [])
    {
        $anchoCorte = round($equip['value'] * 0.8, 2);
        $pasadas = $equip['steps'];
        $haXdia = ($anchoCorte * $pasadas) * 0.8;
        $dieselXDia = $data['diesel_prepare'];
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = $data['tractor_potencia'] * 0.152;
        $llantasXHa = $data['tractor_potencia'] * 0.084;
        $seguroXHa = $data['tractor_potencia'] * 0.017;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultNiveladoraWithAms($equip = [],  $data = [])
    {
        $anchoCorte = round(($equip['value'] * 0.8) * 1.25, 2);
        $pasadas = $equip['steps'] + 0.5;
        $haXdia = ($anchoCorte * $pasadas) * 0.9;
        $dieselXDia = $data['diesel_work'] * 0.9;
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = ($data['tractor_potencia'] * 0.152) * 0.88;
        $llantasXHa = $data['tractor_potencia'] * 0.084;
        $seguroXHa = $data['tractor_potencia'] * 0.017;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultSembradoraWithoutAms($equip = [],  $data = [])
    {
        $anchoCorte = round(($equip['value'] * $data['grooves']) * 0.8, 2);
        $pasadas = $equip['steps'];
        $haXdia = ($anchoCorte * $pasadas) * 0.8;
        $dieselXDia = $data['diesel_work'];
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = $data['tractor_potencia'] * 0.181;
        $llantasXHa = $data['tractor_potencia'] * 0.1;
        $seguroXHa = $data['tractor_potencia'] * 0.021;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultSembradoraWithAms($equip = [],  $data = [])
    {
        $anchoCorte = round((($equip['value'] * $data['grooves']) * 0.8) * 1.25, 2);
        $pasadas = $equip['steps'] + 0.5;
        $haXdia = ($anchoCorte * $pasadas) * 0.9;
        $dieselXDia = $data['diesel_work'] * 0.9;
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = ($data['tractor_potencia'] * 0.181) * 0.88;
        $llantasXHa = $data['tractor_potencia'] * 0.1;
        $seguroXHa = $data['tractor_potencia'] * 0.021;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultFumigadoraWithoutAms($equip = [],  $data = [])
    {
        $anchoCorte = round(($equip['value']) * 0.8, 2);
        $pasadas = $equip['steps'];
        $haXdia = ($anchoCorte * $pasadas) * 0.5;
        $dieselXDia = $data['diesel_work'];
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = $data['tractor_potencia'] * 0.045;
        $llantasXHa = $data['tractor_potencia'] * 0.25;
        $seguroXHa = $data['tractor_potencia'] * 0.0052;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }
    public function ResultFumigadoraWithAms($equip = [],  $data = [])
    {
        $anchoCorte = round(($equip['value']  * 0.8) * 1.26, 2);
        $pasadas = $equip['steps'] + 0.5;
        $haXdia = ($anchoCorte * $pasadas) * 0.7;
        $dieselXDia = $data['diesel_work'] * 0.9;
        $dieselPrecio = $data['diesel_price'];
        $dieselTotal = $dieselXDia * $dieselPrecio;
        $costoHaDiesel = $dieselTotal / $haXdia;
        $operadorXHa = 280 / $haXdia;
        $servicioXHa = ($data['tractor_potencia'] * 0.045) * 0.88;
        $llantasXHa = $data['tractor_potencia'] * 0.25;
        $seguroXHa = $data['tractor_potencia'] * 0.0052;
        $depreciacion = ($data['tractor_value'] / 3650) / $haXdia;
        $costoXHa = $costoHaDiesel + $operadorXHa + $seguroXHa + $llantasXHa + $servicioXHa + $depreciacion;

        return
            [
                'equipment' => $equip,
                'ancho_corte' => $anchoCorte,
                'velocidad_km' => $pasadas,
                'ha_x_dia' => round($haXdia, 2),
                'diesel_x_dia' => $dieselXDia,
                'diesel_precio' => $dieselPrecio,
                'diesel_total' => $dieselTotal,
                'costo_diesel_x_ha' => $costoHaDiesel,
                'operador_x_ha' => $operadorXHa,
                'servicio_x_ha' => $servicioXHa,
                'llantas_x_ha' => $llantasXHa,
                'seguro_x_ha' => $seguroXHa,
                'depreciacion' => $depreciacion,
                'costo_x_ha' => $costoXHa,
            ];
    }

    public function ResultDifferenceAms($EquipmentWitoutAms, $EquipmentWithAms = [])
    {
        $equipment =  $EquipmentWitoutAms['equipment'];
        $totalCostWithoutAms = round($EquipmentWitoutAms['costo_x_ha'], 2);
        $totalCostWithAms = round($EquipmentWithAms['costo_x_ha'], 2);
        $totalDiff = round($totalCostWithoutAms - $totalCostWithAms, 2);
        $percentDiff = round(($totalDiff / $totalCostWithAms) * 100, 2);
        $save = round($equipment['steps'] * $totalDiff, 2);
        $totalSave = round($equipment['steps'] * $save, 2);

        return [
            'equipment' => $equipment,
            'total_cost_without_ams' => $totalCostWithoutAms,
            'total_cost_with_ams' => $totalCostWithAms,
            'total_diff' => $totalDiff,
            'percent_diff' => $percentDiff,
            'save' => $save,
            'total_save' => $totalSave,
        ];
    }

    public function ResultSaveAms($resultDiff, $data)
    {
        $cycle = $data['cycles'];
        $amsValue = $data['ams_value'];
        $totalSaveCycle = array_reduce($resultDiff, function ($sum, $item) {
            $sum += $item['total_save'];
            return $sum;
        });
        $totalSaveYear = $cycle * $totalSaveCycle;
        $returnInvestment = round((($amsValue / $totalSaveYear) * 12) * 30, 2);
        $saveXHa = round($totalSaveYear / $data['hectares'], 2);
        $engache = round($amsValue * .44, 2);
        $paymentSemestral = round(($amsValue * .6) / 4, 2);

        return [
            'total_save_x_cycle' => $totalSaveCycle,
            'cycles_year' => $cycle,
            'total_save_year' => $totalSaveYear,
            'ams_value' => $amsValue,
            'return_investment' => $returnInvestment,
            'save_x_ha' => $saveXHa,
            'enganche' => $engache,
            'payments_semestral' => $paymentSemestral,
        ];
    }

    /**
     * Constantes
     * Diesel sin AMS = Diesel Presparacion
     * Diesel con AMS = Diesel Preparacion * .9
     * 
     */
}

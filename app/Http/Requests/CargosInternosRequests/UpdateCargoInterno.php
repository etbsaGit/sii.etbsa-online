<?php

namespace App\Http\Requests\CargosInternosRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCargoInterno extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'description' => 'required',
            'num_economico' => 'required',
            'num_cliente' => 'required',
            'nip' => 'required',
            'concepto_cargo' => 'required',
            'ot' => 'required',
            'mano_obra' => 'required',
            'refacciones' => 'required',
            'kilometraje' => 'required',
            'foraneo' => 'required',
            'amount' => 'required',
            'cargos_sucursal' => 'required|array',
        ];
    }
}
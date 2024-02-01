<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Components\Core\ResponseHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePurchaseOrderRequest extends FormRequest
{

    use ResponseHelpers;
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
            'supplier_id' => 'required',
            'subtotal' => 'required',
            'discount' => 'required',
            'total' => 'required',
            'charges' => 'required|Array',
            'products' => 'required|Array',
            'products.*.c_ClaveProdServ' => 'required',
            'payment_condition' => 'required',
            'purchase_concept_id' => 'required',
            'observation' => 'required',
            'uso_cfdi' => 'required',
            'metodo_pago' => 'required',
            'forma_pago' => 'required',
            'file' => 'array',
            // 'file.*' => 'file|mimes:pdf|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'Agrege Al menos un Product',
            'products.*.c_ClaveProdServ.required' => 'Todos los Productos deben de Tener una Clave de Producto',
            // 'file.*.mimes' => 'El Archivo de ser PDF',
            // 'file.*.file' => 'Debe un Archivo',
            // 'file.*.max' => 'Debe un Archivo',
        ];
    }

    protected function prepareForValidation()
    {
        // Obtener el payload deserializado como un array
        $payload = json_decode($this->input('payload'), true);
        // Reemplazar la clave 'payload' con los datos deserializados
        $this->replace($payload);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponseBadRequest($validator->errors()->first())
        );
    }



}
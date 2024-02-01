<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Components\Core\ResponseHelpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStatusPurchaseOrderRequest extends FormRequest
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
            'estatus_key' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'estatus.required' => 'El estatus es Requerido',
        ];
    }

    // protected function prepareForValidation()
    // {
    //     // Obtener el payload deserializado como un array
    //     $payload = json_decode($this->input('payload'), true);
    //     // Reemplazar la clave 'payload' con los datos deserializados
    //     $this->replace($payload);
    // }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponseBadRequest($validator->errors()->first())
        );
    }



}
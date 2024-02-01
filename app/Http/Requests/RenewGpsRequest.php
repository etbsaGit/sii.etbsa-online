<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Components\Gps\Repositories\GpsRepository;
use Illuminate\Support\Facades\DB;

class RenewGpsRequest extends FormRequest
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
            'invoice' => 'required',
            'amount' => 'required|numeric',
            'currency' => 'required',
            'exchange_rate' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'invoice.required' => 'Folio Factura es Obligatoria',
        ];
    }

    public function renewGPS($gps)
    {
        DB:transaction(function(){
            $data = $this->validated();

            $gps->history()->create([]);

            // $history = Historty::Create($gps);

            $gps->update($data);


        });
    }
}

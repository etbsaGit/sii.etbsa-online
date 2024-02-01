<?php

namespace App\Http\Controllers\Admin;

use App\Components\Vehicle\Models\VehicleTicketCard;
use App\Components\Vehicle\Repositories\VehicleTicketCardRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleTicketCardController extends AdminController
{

    private $vehicleTicketCardRepository;

    /**
     * vehicleController constructor.
     * @param vehicleTicketCardRepository $vehicleTicketCardRepository
     */
    public function __construct(VehicleTicketCardRepository $vehicleTicketCardRepository)
    {
        $this->vehicleTicketCardRepository = $vehicleTicketCardRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)

    {
        if (!$request->has('order_by')) {
            $request['order_by'] = 'ticket_card';
        }

        $data = $this->vehicleTicketCardRepository->list($request->all());

        return $this->sendResponseOk($data, "list vehicle ticket cards.");
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
            'ticket_card' => 'required',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $ticket_card = $this->vehicleTicketCardRepository->create($request->all());

        if (!$ticket_card) {
            return $this->sendResponseBadRequest("Failed create.");
        }

        return $this->sendResponseCreated($ticket_card);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $vehicle_ticket_card)
    {
        $validate = validator($request->all(), [
            'account_balance' => 'required',
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        // return dd();
        $payload = $request->all();
        $updated = $this->vehicleTicketCardRepository->update($vehicle_ticket_card, $payload);

        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update");
        }

        return $this->sendResponseUpdated();
    }

    public function resetBalance(Request $request)
    {
        $ids = explode(',', $request['id']);
        foreach ($ids as $id) {
            $VehicleTicketCard = VehicleTicketCard::find($id);
            if (!$VehicleTicketCard) {
                continue;
            };
            $VehicleTicketCard->account_balance = 0;
            $VehicleTicketCard->save();
        }
        return $this->sendResponseOk([], "Tickets Cards Reseteadas");;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\ExchangeRates;
use App\Components\Product\Repositories\ExchangeRatesRepository;
use Illuminate\Http\Request;

class ExchangeRatesController extends AdminController
{

    private $exchangeReposiroty;

    public function __construct(ExchangeRatesRepository $exchangeReposiroty)
    {
        $this->exchangeReposiroty = $exchangeReposiroty;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->exchangeReposiroty->list(request()->all());
        return $this->sendResponseOk($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $exchangesRate = $this->exchangeReposiroty->create($request->all());
        if (!$exchangesRate) {
            return $this->sendResponseBadRequest("Failed create.");
        }
        return $this->sendResponseCreated($exchangesRate);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExchangeRates  $exchangeRates
     * @return \Illuminate\Http\Response
     */
    public function show(ExchangeRates $exchange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ExchangeRates  $exchange
     * @return \Illuminate\Http\Response
     */
    public function edit(ExchangeRates $exchange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ExchangeRates  $exchange
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExchangeRates $exchange)
    {
        $validate = validator($request->all(), []);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $exchange->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed update.");
        }
        return $this->sendResponseUpdated($updated);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExchangeRates  $exchange
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExchangeRates $exchange)
    {
        //
    }
}

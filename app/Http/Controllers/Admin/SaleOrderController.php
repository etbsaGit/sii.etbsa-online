<?php

namespace App\Http\Controllers\Admin;

use App\Components\Customers\Models\Customers;
use DB;
use Illuminate\Http\Request;

class SaleOrderController extends AdminController
{

    public function __construct()
    {
    }

    public function index()
    {
    }

    public function create()
    {
        $customers = DB::table('customers')->get(['id', 'full_name', 'rfc']);

        return $this->sendResponseOk(compact('customers'), "create sale order.");
    }

    public function store(Request $request)
    {
    }

    public function show()
    {
    }


    public function edit()
    {
    }

    public function update(Request $request)
    {
    }


    public function destroy()
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\cClaveProdServ;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;

class CClaveProdServController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = request()->all();
        $data = cClaveProdServ::filter($params ?? [])
            ->search($params['search'] ?? '')
            ->get();
        return $this->sendResponseOk($data);
    }

    public function getByClvProd()
    {
        $params = request()->all();
        $data = cClaveProdServ::getClvProd($params["ClvProd"] ?? '')->first(['c_ClaveProdServ', 'Descripción', 'Palabrassimilares']);

        return $this->sendResponseOk($data);
    }
}
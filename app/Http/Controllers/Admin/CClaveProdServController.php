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
        $data = cClaveProdServ::search($params['search'] ?? '')->get();
        return $this->sendResponseOk($data);
    }
}
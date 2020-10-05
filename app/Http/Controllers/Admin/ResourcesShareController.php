<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;

class ResourcesShareController extends AdminController
{
    public function getEstates()
    {
        $estates = DB::table('estates')->get(['id', 'name']);
        return $this->sendResponseOk(compact('estates'));
    }
    public function getTownship($id)
    {
        $townships = DB::table('townships')->where('estate_id', $id)->get(['id', 'name']);
        return $this->sendResponseOk(compact('townships'));
    }
}

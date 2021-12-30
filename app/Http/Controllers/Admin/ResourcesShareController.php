<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;

class ResourcesShareController extends AdminController
{
    public function getUser()
    {
        $users = DB::table('users')->get(['id', 'name']);
        return $this->sendResponseOk(compact('users'));
    }

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

    public function getAgencies()
    {
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);

        return $this->sendResponseOk(compact('agencies'));
    }

    public function getDepartment()
    {
        $departments = DB::table('departments')->get(['id', 'title']);
        return $this->sendResponseOk(compact('departments'));
    }

    public function getProspect()
    {
        $prospects = DB::table('prospect')->get(['id', 'full_name', 'phone']);
        return $this->sendResponseOk(compact('prospects'));
    }

    public function getOptions()
    {
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        $departments = DB::table('departments')->get(['id', 'title']);
        $estates = DB::table('estates')->get(['id', 'name']);

        return $this->sendResponseOk(compact('estates', 'agencies', 'departments'));
    }
}

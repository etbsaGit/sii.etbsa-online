<?php

namespace App\Http\Controllers\Admin;

use App\Components\Common\Models\Estatus;
use App\Components\RRHH\Models\EmployeeRecruitment;
use App\Components\RRHH\Repositories\EmployeeRecruitmentRepository;
use Auth;
use DB;
use Illuminate\Http\Request;

class EmployeeRecruitmentController extends AdminController
{
    private $recruitmentRepository;

    public function __construct(EmployeeRecruitmentRepository $recruitmentRepository)
    {
        $this->recruitmentRepository = $recruitmentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->recruitmentRepository->list(request()->all());
        return $this->sendResponseOk($data, "list  ok.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['estatus_id'] = Estatus::where('key', Estatus::ESTATUS_PENDIENTE)->first()->id;
        $request['created_by'] = Auth::user()->id;
        $validate = validator($request->all(), []);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $recruitment = $this->recruitmentRepository->create($request->all());

        if (!$recruitment) {
            return $this->sendResponseBadRequest("Failed create.");
        }

        return $this->sendResponseCreated($recruitment);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeRecruitment $recruitment)
    {

        return $this->sendResponseOk($recruitment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeRecruitment $recruitment)
    {
        $request['estatus_id'] = Estatus::where(
            'key',
            $request['estatus_key'] ? $request['estatus_key'] : $request['estatus_id']
        )->first()->id;
        $validate = validator($request->all(), [
            'estatus_id' => 'required'
        ]);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $updated = $recruitment->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed to update");
        }
        return $this->sendResponseUpdated([$request->all(), $updated]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        return $this->sendResponseDeleted();
    }

    public function options()
    {
        $estatus = DB::table('estatus')->get(['id', 'title', 'key']);
        return $this->sendResponseOk(compact(
            'estatus',
        ), "list Resources.");
    }
}

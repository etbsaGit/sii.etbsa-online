<?php

namespace App\Http\Controllers\Admin;

use App\Components\RRHH\Models\DirectBoss;
use App\Components\RRHH\Models\Employee;
use App\Components\RRHH\Repositories\EmployeeRepository;
use App\Components\User\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends AdminController
{
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->employeeRepository->list(request()->all());
        return $this->sendResponseOk($data, "list  ok.");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //resources form
        $agencies = DB::table('agencies')->get(['id', 'code', 'title']);
        $departments = DB::table('departments')->get(['id', 'title']);
        $direct_boss = Employee::all('id', 'name', 'last_name');
        return $this->sendResponseOk(compact(
            'agencies',
            'departments',
            'direct_boss',
        ), "list Resources orders ok.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['created_by'] = Auth::user()->id;
        $validate = validator($request->all(), [
            'photo' => ['nullable', 'image'],
        ]);

        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }

        $employee = $this->employeeRepository->create(
            [
                $request->all(),
                'photo_path' => $request->file('photo') ? $request->file('photo')->store('avatares') : null,
            ]
        );

        if (!$employee) {
            return $this->sendResponseBadRequest("Failed create.");
        }

        return $this->sendResponseCreated($employee);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        $data = [
            'id' => $employee->id,
            'name' => $employee->full_name,
            'number_employee' => $employee->number_employee ?? '',
            'agency' => $employee->agency->title ?? '',
            'department' => $employee->department->title ?? '',
            'job' => $employee->job_title ?? '',
            'phone' => $employee->phone ?? '',
            'address' => $employee->address . ' ' . $employee->colonia . ',' . $employee->code_postal,
            'estado' => $employee->township->estate->name ?? '',
            'municipio' => $employee->township->name ?? '',
            'boss' => $employee->boss->full_name ?? '',
            'user' => $employee->user->email ?? null,
            'user_id' => $employee->user->id ?? null,
            'photo' => $employee->profile_photo_url,
        ];

        return $this->sendResponseOk($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        return $this->sendResponseOk($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $validate = validator($request->all(), [
            'photo' => ['nullable', 'image'],
        ]);
        if ($validate->fails()) {
            return $this->sendResponseBadRequest($validate->errors()->first());
        }
        $updated = $employee->update($request->all());
        if (!$updated) {
            return $this->sendResponseBadRequest("Failed to update");
        }
        if ($request->file('photo')) {
            if (Storage::disk('s3')->exists($employee->photo_path)) {
                $delete = Storage::disk('s3')->delete($employee->photo_path);
            }
            $employee->update(['photo_path' => $request->file('photo')->store('avatares/' . $employee->id, 's3')]);
        }
        return $this->sendResponseUpdated($updated);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
    }


    public function assignedUser(Employee $employee, User $user)
    {
        if ($employee->user) {
            $employee->user->profiable()->dissociate();
            $employee->user->save();
        }
        $user->profiable()->associate($employee);
        $user->save();
        return $this->sendResponseOk([], 'Usuario Asignado');
    }
    public function assignedBoss(DirectBoss $employee_boss, Employee $employee)
    {
        $employee->boss()->associate($employee_boss);
        $employee->save();
        return $this->sendResponseOk([], 'Jefe Directo Asignado');
    }

    public function options()
    {
        $users = DB::table('users')->whereNull('profiable_id')->get(['id', 'name', 'email']);
        return $this->sendResponseOk(compact(
            'users',
        ), "list Resources orders ok.");
    }
}

<?php

namespace Modules\Employee\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Privilege\Repositories\UserRepository;

class EmployeeSupervisorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        UserRepository $user
    )
    {
        $this->employees = $employees;
        $this->user = $user;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'employees'=>$this->employees->get()
        ], 200);
    }

    /**
     * Display the specified province.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupervisors($id)
    {
        $employee = $this->employees->with('latestTenure')->find($id);
        $supervisors = $this->user->select('*')
                            ->whereIn('employee_id', [$employee->latestTenure->supervisor_id,
                                                        $employee->latestTenure->cross_supervisor_id,
                                                        $employee->latestTenure->next_line_manager_id])
                            ->get();

        if($supervisors){
            return response()->json([
                'supervisors'=>$supervisors,
            ], 200);
        }
    }
}

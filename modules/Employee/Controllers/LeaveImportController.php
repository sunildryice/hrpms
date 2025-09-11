<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Imports\EmployeeLeaveImport;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;


use Excel;

class LeaveImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  LeaveRepository $leaves
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        LeaveRepository $leaves
    )
    {
        $this->employees = $employees;
        $this->leaves = $leaves;
    }

    /**
     * Show the form for importing the leave of employees.
     *
     * @return mixed
     */
    public function create()
    {
        return view('Employee::Leave.import');
    }

    /**
     * Import leave of employees in storage.
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        Excel::import(new EmployeeLeaveImport(), request()->file('attachment'));

        return back()->withSuccessMessage('Leave records are imported successfully.');
    }
}

<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeHourRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Requests\Hour\StoreRequest;
use Modules\Employee\Requests\Hour\UpdateRequest;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\OfficeRepository;

class EmployeeHourController extends Controller
{
    protected $destinationPath;

    public function __construct(
        protected DepartmentRepository $departments,
        protected DesignationRepository $designations,
        protected DistrictRepository $districts,
        protected EmployeeRepository $employees,
        protected OfficeRepository $offices,
        protected EmployeeHourRepository $hours
    ) {
        $this->destinationPath = 'employees';
    }

    public function store(StoreRequest $request, $employee)
    {
        $employee = $this->employees->find($employee);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = $inputs['updated_by'] = auth()->id();

        $checkExists = $employee->hours()
            ->where(function ($q) use ($inputs) {
                $q->where(function ($query) use ($inputs) {
                    $query->whereDate('start_date', '<=', $inputs['start_date'])
                        ->whereDate('end_date', '>=', $inputs['start_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('start_date', '<=', $inputs['end_date'])
                        ->whereDate('end_date', '>=', $inputs['end_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('start_date', '>', $inputs['start_date'])
                        ->whereDate('end_date', '<', $inputs['end_date']);
                });
            })->first();

        if ($checkExists) {
            return redirect()->back()->withInput()
                ->withErrorMessage('Working hour already exists in the selected date range');
        }

        $hour = $this->hours->create($inputs);
        if ($hour) {
            if ($employee->user) {
                Artisan::call('dryice:reconcile:employee:leave', ['employee' => $hour->employee->employee_code]);
            }
            return redirect()->back()
                ->withSuccessMessage('Working hour detail is successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Working hour detail can not be added.');
    }

    public function edit(Request $request, $employeeId, $id)
    {
        $hour = $this->hours->with(['employee'])->find($id);
        if ($request->wantsJson()) {
            return response()->json([
                'hour' => $hour,
                'start_date' => $hour->start_date ? $hour->start_date->format('Y-m-d') : '',
                'end_date' => $hour->end_date ? $hour->end_date->format('Y-m-d') : '',
                'updateAction' => route('employees.hours.update', [$hour->employee_id, $hour->id]),
            ]);
        }

        return view('Employee::Hour.edit')
            ->with(['hour' => $hour]);
    }

    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $employee = $this->employees->find($employeeId);
        $hour = $this->hours->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();

        $checkExists = $employee->hours()
            ->whereNotIn('id', [$hour->id])
            ->where(function ($q) use ($inputs) {
                $q->where(function ($query) use ($inputs) {
                    $query->whereDate('start_date', '<=', $inputs['start_date'])
                        ->whereDate('end_date', '>=', $inputs['start_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('start_date', '<=', $inputs['end_date'])
                        ->whereDate('end_date', '>=', $inputs['end_date']);
                })->orWhere(function ($query) use ($inputs) {
                    $query->whereDate('start_date', '>', $inputs['start_date'])
                        ->whereDate('end_date', '<', $inputs['end_date']);
                });
            })->first();

        if ($checkExists) {
            return redirect()->back()->withInput()
                ->withErrorMessage('Working hour already exists in the selected date range');
        }

        $hour = $this->hours->update($id, $inputs);

        if ($hour) {
            if ($employee->user) {
                Artisan::call('dryice:reconcile:employee:leave', ['employee' => $hour->employee->employee_code]);
            }

            return redirect()->back()->withInput()
                ->withSuccessMessage('Working hour detail is successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Working hour can not be updated.');
    }
}

<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\TrainingRepository;
use Modules\Employee\Requests\Training\StoreRequest;
use Modules\Employee\Requests\Training\UpdateRequest;

class TrainingController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected TrainingRepository $trainings
    ) {
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee education in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $employeeId)
    {
        //        $this->authorize('manage-employee');
        $employee = $this->employees->find($employeeId);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['created_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $employee->id, time() . '_training.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $training = $this->trainings->create($inputs);
        if ($training) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee training detail is successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee training detail can not be added.');
    }

    /**
     * Show the form for editing the specified employee family member.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $employeeId, $id)
    {
        $training = $this->trainings->with(['employee'])->find($id);
        $attachment = '';
        if ($training->attachment != null) {
            $attachment = asset('storage/' . $training->attachment);
        }
        if ($request->wantsJson()) {
            return response()->json([
                'training' => $training,
                'attachment' => $attachment,
                'period_from' => $training->period_from ? $training->period_from->format('Y-m-d') : '',
                'period_to' => $training->period_to ? $training->period_to->format('Y-m-d') : '',
                'updateAction' => route('employees.trainings.update', [$employeeId, $training->id]),
            ]);
        }

        return view('Employee::Training.edit')
            ->withFamilyMember($training);
    }

    /**
     * Update the specified employee training in storage.
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $training = $this->trainings->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $training->employee->id, time() . '_training.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $training = $this->trainings->update($id, $inputs);

        if ($training) {
            return redirect()->back()->withInput()
                ->withSuccessMessage('Employee training detail is successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee training can not be updated.');
    }
}

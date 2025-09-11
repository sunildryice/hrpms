<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\ExperienceRepository;
use Modules\Employee\Requests\Experience\StoreRequest;
use Modules\Employee\Requests\Experience\UpdateRequest;

class ExperienceController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  ExperienceRepository $experiences
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected ExperienceRepository $experiences
    )
    {
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee education in storage.
     *
     * @param  \Modules\Employee\Requests\Experience\StoreRequest $request
     * @return \Illuminate\Http\Response
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
                ->storeAs($this->destinationPath .'/'.$employee->id, time().'_experience.'. $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }

        $family = $this->experiences->create($inputs);
        if($family){
        return redirect()->back()->withInput()
                ->withSuccessMessage('Employee experience detail is successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee experience detail can not be added.');
    }

    /**
     * Show the form for editing the specified employee family member.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $employeeId, $id)
    {
        $experience = $this->experiences->with(['employee'])->find($id);
        $attachment  = '';
        if($experience->attachment != NULL){
            $attachment = asset('storage/'.$experience->attachment);
        }
        if($request->wantsJson()){
            return response()->json([
                'experience'=>$experience,
                'attachment'=>$attachment,
                'period_from'=>$experience->period_from ? $experience->period_from->format('Y-m-d') : '',
                'period_to'=>$experience->period_to ? $experience->period_to->format('Y-m-d') : '',
                'updateAction'=>route('employees.experiences.update', [$employeeId, $experience->id]),
            ]);
        }

        return view('Employee::Experience.edit')
            ->withFamilyMember($experience);
    }

    /**
     * Update the specified employee experience in storage.
     *
     * @param \Modules\Employee\Requests\Experience\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $experience = $this->experiences->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath .'/'.$experience->employee->id, time().'_experience.'. $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $experience = $this->experiences->update($id, $inputs);

        if($experience){
        return redirect()->back()->withInput()
                ->withSuccessMessage('Employee experience detail is successfully updated.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Employee experience can not be updated.');
    }
}

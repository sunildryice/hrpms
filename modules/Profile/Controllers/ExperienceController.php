<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\ExperienceRepository;
use Modules\Employee\Requests\Experience\StoreRequest;
use Modules\Employee\Requests\Experience\UpdateRequest;

class ExperienceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  ExperienceRepository $experiences
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        ExperienceRepository $experiences
    )
    {
        $this->employees = $employees;
        $this->experiences = $experiences;
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee education in storage.
     *
     * @param  \Modules\Employee\Requests\Experience\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $employee = $this->employees->find($authUser->employee_id);
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
            return redirect()->route('profile.edit', ['tab'=>'experience-details'])
                ->withSuccessMessage('Experience detail is successfully added.');
        }
        return redirect()->route('profile.edit', ['tab'=>'experience-details'])
            ->withInput()
            ->withWarningMessage('Experience detail can not be added.');
    }

    /**
     * Show the form for editing the specified employee family member.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id)
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
                'updateAction'=>route('profile.experiences.update', [$experience->id]),
            ]);
        }

        return view('Profile::Experience.edit')
            ->withFamilyMember($experience);
    }

    /**
     * Update the specified employee experience in storage.
     *
     * @param \Modules\Employee\Requests\Experience\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
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
            return redirect()->route('profile.edit', ['tab'=>'experience-details'])
                ->withSuccessMessage('Experience detail is successfully updated.');
        }
        return redirect()->route('profile.edit', ['tab'=>'experience-details'])
            ->withInput()
            ->withWarningMessage('Experience can not be updated.');
    }
}

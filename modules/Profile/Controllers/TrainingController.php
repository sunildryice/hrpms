<?php

namespace Modules\Profile\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\TrainingRepository;
use Modules\Profile\Requests\Training\StoreRequest;
use Modules\Profile\Requests\Training\UpdateRequest;

class TrainingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository $employees
     * @param  TrainingRepository $trainings
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        TrainingRepository $trainings
    )
    {
        $this->employees = $employees;
        $this->trainings = $trainings;
        $this->destinationPath = 'employees';
    }

    /**
     * Store a newly created employee education in storage.
     *
     * @param  \Modules\Employee\Requests\Training\StoreRequest $request
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
                ->storeAs($this->destinationPath .'/'.$employee->id, time().'_training.'. $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $training = $this->trainings->create($inputs);
        if($training){
            return redirect()->route('profile.edit', ['tab'=>'training-details'])
                ->withSuccessMessage('Training detail is successfully added.');
        }
        return redirect()->route('profile.edit', ['tab'=>'training-details'])
            ->withInput()
            ->withWarningMessage('Training detail can not be added.');
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
        $training = $this->trainings->with(['employee'])->find($id);
        $attachment  = '';
        if($training->attachment != NULL){
            $attachment = asset('storage/'.$training->attachment);
        }
        if($request->wantsJson()){
            return response()->json([
                'training'=>$training,
                'attachment'=>$attachment,
                'period_from'=>$training->period_from ? $training->period_from->format('Y-m-d') : '',
                'period_to'=>$training->period_to ? $training->period_to->format('Y-m-d') : '',
                'updateAction'=>route('profile.trainings.update', [$training->id]),
            ]);
        }

        return view('Profile::Training.edit')
            ->withFamilyMember($training);
    }

    /**
     * Update the specified employee training in storage.
     *
     * @param \Modules\Employee\Requests\Training\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        $training = $this->trainings->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath .'/'.$training->employee->id, time().'_training.'. $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $training = $this->trainings->update($id, $inputs);

        if($training){
            return redirect()->route('profile.edit', ['tab'=>'training-details'])
                ->withSuccessMessage('Training detail is successfully updated.');
        }
        return redirect()->route('profile.edit', ['tab'=>'training-details'])
            ->withInput()
            ->withWarningMessage('Training can not be updated.');
    }
}

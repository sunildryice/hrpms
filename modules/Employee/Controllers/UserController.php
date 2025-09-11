<?php

namespace Modules\Employee\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\Employee\Mail\SendInvitation;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Requests\User\StoreRequest;
use Modules\Employee\Requests\User\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected UserRepository $users
    ) {
    }

    /**
     * Store a newly created employee in storage.
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
        $inputs['roles'] = explode(',', $request->role_ids);
        $inputs['created_by'] = auth()->id();
        $user = $this->employees->createUser($employee, $inputs);

        if ($user) {
            if ($user->email_address && $user->reset_token) {
                Mail::to($user->email_address)
                    ->send(new SendInvitation($employee));
            }

            return redirect()->route('employees.index')
                ->withSuccessMessage('Employee user is successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee user can not be added.');
    }

    /**
     * Update the specified employee user in storage.
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        $employee = $this->employees->find($id);
        $inputs = $request->validated();
        $inputs['roles'] = explode(',', $request->role_ids);
        $inputs['updated_by'] = auth()->id();
        $user = $this->employees->updateUser($employee, $inputs);

        if ($user) {
            return redirect()->route('employees.index')
                ->withSuccessMessage('Employee user '.$user->getFullName().' is successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Employee user can not be updated.');
    }

    public function storeConsultant(StoreRequest $request, $employeeId)
    {
        //        $this->authorize('manage-employee');
        $employee = $this->employees->find($employeeId);
        $inputs = $request->validated();
        $inputs['employee_id'] = $employee->id;
        $inputs['roles'] = explode(',', $request->role_ids);
        $inputs['created_by'] = auth()->id();
        $user = $this->employees->createUser($employee, $inputs);

        if ($user) {
            if ($user->email_address && $user->reset_token) {
                Mail::to($user->email_address)
                    ->send(new SendInvitation($employee));
            }

            return redirect()->route('consultant.index')
                ->withSuccessMessage('Consultant user is successfully added.');
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Consultant user can not be added.');
    }

    public function updateConsultant(UpdateRequest $request, $id)
    {
        $employee = $this->employees->find($id);
        $inputs = $request->validated();
        $inputs['roles'] = explode(',', $request->role_ids);
        $inputs['updated_by'] = auth()->id();
        $user = $this->employees->updateUser($employee, $inputs);

        if ($user) {
            return redirect()->route('consultant.index')
                ->withSuccessMessage('Consultant user '.$user->getFullName().' is successfully updated.');
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Consultant user can not be updated.');
    }
}

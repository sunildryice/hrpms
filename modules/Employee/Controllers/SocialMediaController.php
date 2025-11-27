<?php

namespace Modules\Employee\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeSocialMediaRepository;
use Modules\Employee\Requests\SocialMedia\UpdateRequest;
use Modules\Master\Repositories\SocialMediaAccountRepository;

class SocialMediaController extends Controller
{

    public function __construct(
        protected SocialMediaAccountRepository $socialMediaAccount,
        protected EmployeeSocialMediaRepository $employeeSocialMediaRepository
    ) {}


    public function update(UpdateRequest $request, Employee $employee)
    {
        try {
            $inputs = $request->validated();

            $socialMediaAccounts = $this->socialMediaAccount->pluck('title', 'id');

            foreach ($socialMediaAccounts as $id => $account) {
                $field = strtolower($account);
                $inputs[$field] = $inputs[$field] ?? null;

                $this->employeeSocialMediaRepository->updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'social_account_id' => $id,
                    ],
                    [
                        'link' => $inputs[$field] ?? null,
                        'social_account_id' => $id,
                        'updated_by' => auth()->id(),
                    ]
                );
            }

            $employee->bio = $inputs['bio'] ?? null;
            $employee->save();

            return redirect()
                ->route('employees.edit', ['employee' => $employee->id, 'tab' => 'social-media'])
                ->with('success_message', 'Social media links updated successfully.');
        } catch (\Exception $e) {


            return redirect()
                ->route('employees.edit', ['employee' => $employee->id, 'tab' => 'social-media'])
                ->with('error_message', 'An error occurred while updating social media links: ' . $e->getMessage());
        }
    }
}

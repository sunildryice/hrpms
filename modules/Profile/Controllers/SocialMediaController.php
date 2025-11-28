<?php

namespace Modules\Profile\Controllers;

use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeSocialMediaRepository;
use Modules\Master\Repositories\SocialMediaAccountRepository;
use Modules\Employee\Requests\SocialMedia\UpdateRequest;
use Modules\Employee\Models\Employee;

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
                ->route('profile.edit', ['tab' => 'social-media'])
                ->with('success_message', 'Social media links updated successfully.');
        } catch (\Exception $e) {


            return redirect()
                ->route('profile.edit', ['tab' => 'social-media'])
                ->with('error_message', 'An error occurred while updating social media links: ' . $e->getMessage());
        }
    }
}

<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Modules\ExitStaffClearance\Models\StaffClearanceDepartment;
use Modules\ExitStaffClearance\Models\StaffClearanceLog;
use Modules\ExitStaffClearance\Notifications\StaffClearanceVerified;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Modules\ExitStaffClearance\Requests\Verify\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;

class StaffClearanceVerifyController extends Controller
{
    public function __construct(
        protected StaffClearanceRepository $staffClearance,
        protected StaffClearanceLog $staffClearanceLog,
        protected StaffClearanceDepartment $clearanceDepartments,
        protected UserRepository $users
    ) {
    }

    public function store(StoreRequest $request, $clearanceId)
    {

        $staffClearance = $this->staffClearance->find($clearanceId);
        $this->authorize('verify', $staffClearance);
        $inputs = $request->validated();

        $staffClearance = $this->staffClearance->verify($clearanceId, $inputs);

        if ($staffClearance) {

            // $staffClearance->endorser->notify(new StaffClearanceVerified($staffClearance));
            foreach($this->users->permissionBasedUsers('hr-staff-clearance') as $user){
                $user->notify(new StaffClearanceVerified($staffClearance));
            }

            return redirect()->route('staff.clearance.index')->withSuccessMessage('Staff Clearance successfully updated.');
        }

        return redirect()->back()->withErrorMessage('Staff Clearance Cannot be updated');
    }
}

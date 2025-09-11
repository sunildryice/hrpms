<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ExitStaffClearance\Models\StaffClearanceLog;
use Modules\ExitStaffClearance\Models\StaffClearanceDepartment;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Modules\ExitStaffClearance\Requests\Certify\StoreRequest;
use Modules\ExitStaffClearance\Notifications\StaffClearanceCertified;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class StaffClearanceCertifyController extends Controller
{
    public function __construct(
        protected StaffClearanceRepository $staffClearance,
        protected StaffClearanceLog $staffClearanceLog,
        protected StaffClearanceDepartment $clearanceDepartments,
        protected UserRepository $users
    )
    {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->staffClearance->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
                                            ->where('reviewer_id', '=', $authUser->id)
                                            ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($staffClearance) {
                    return $staffClearance->getEmployeeName();
                })
                ->addColumn('fiscal_year', function ($staffClearance) {
                    return $staffClearance->getFiscalYear();
                })
                ->addColumn('review_type', function ($staffClearance) {
                    return $staffClearance->getReviewType();
                })
                ->addColumn('review_from', function ($staffClearance) {
                    return $staffClearance->getReviewFromDate();
                })
                ->addColumn('review_to', function ($staffClearance) {
                    return $staffClearance->getReviewToDate();
                })
                ->addColumn('status', function ($staffClearance) {
                    return '<span class="' . $staffClearance->getStatusClass() . '">' . $staffClearance->getStatus() . '</span>';
                })
                ->addColumn('action', function ($staffClearance) use($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('performance.review.create', [$staffClearance->id]).'" rel="tooltip" title="Review Performance Review Form"><i class="bi bi-ui-checks"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('ExitStaffClearance::Review.index');
    }

    public function create(Request $request, $id)
    {
        $staffClearance = $this->staffClearance->find($id);

        $this->authorize('review', $staffClearance);

        $record = array(
            'performanceReview' => $staffClearance,
            'groupBQuestions' => $this->clearanceDepartments->where('group', 'B')->orderBy('position')->get(),
            'groupDQuestions' => $this->clearanceDepartments->where('group', 'D')->orderBy('position')->get(),
            'groupEQuestions' => $this->clearanceDepartments->where('group', 'E')->orderBy('position')->get(),
            'groupFQuestions' => $this->clearanceDepartments->where('group', 'F')->orderBy('position')->get(),
            'groupGQuestions' => $this->clearanceDepartments->where('group', 'G')->orderBy('position')->get(),
            'groupHQuestions' => $this->clearanceDepartments->where('group', 'H')->orderBy('position')->get(),
            'groupIQuestions' => $this->clearanceDepartments->where('group', 'I')->orderBy('position')->get(),
            'groupJQuestions' => $this->clearanceDepartments->where('group', 'J')->orderBy('position')->get(),
            'currentKeyGoals' => $staffClearance->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $staffClearance->keyGoals->where('type', '=', 'future'),
        );

        $nextLineManagerExists = $staffClearance->employee->getNextLineManagerUserId() == '' ? false : true;

        if ($nextLineManagerExists) {
            $receivers = [$staffClearance->employee->latestTenure->nextLineManager->user];
        } else {
           $receivers =  $this->user->permissionBasedUsers('approve-performance-review');
        }

        if ($staffClearance->getReviewType() == 'Annual Review') {
            $midTermReview = $this->staffClearance->where('fiscal_year_id', '=', $staffClearance->fiscal_year_id)
                                                    ->where('review_type_id', '=', 2) //For mid-term review
                                                    ->where('employee_id', $staffClearance->employee_id)
                                                    ->first();
            $keyGoalReview = $this->staffClearance->where('fiscal_year_id', '=', $staffClearance->fiscal_year_id)
                                                    ->where('review_type_id', '=', 3) //For key-goal review
                                                    ->where('employee_id', $staffClearance->employee_id)
                                                    ->first();
            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            $professionalDevelopmentPlanQuestion = $this->clearanceDepartments->where('group', 'E')
                                                                                ->orderBy('position', 'desc')
                                                                                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            $array = [
                'midTermReview'                         => $midTermReview,
                'keyGoalReview'                         => $keyGoalReview,
                'keygoals'                              => $keygoals,
                'professionalDevelopmentPlanQuestion'   => $professionalDevelopmentPlanQuestion,
                'professionalDevelopmentPlan'           => $professionalDevelopmentPlan,
                'nextLineManagerExists'                 => $nextLineManagerExists,
                'receivers'                             => $receivers,
                'authUser'                              => auth()->user(),
            ];

            return view('ExitStaffClearance::Review.AnnualPerformanceReview.create', $record, $array);

        } elseif ($staffClearance->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->staffClearance->where('fiscal_year_id', '=', $staffClearance->fiscal_year_id)
                                                    ->where('review_type_id', '=', 3)
                                                    ->where('employee_id', $staffClearance->employee_id)
                                                    ->first();
            $professionalDevelopmentPlanQuestion = $this->clearanceDepartments->where('group', 'E')
                                                                                ->orderBy('position', 'desc')
                                                                                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            $array = [
                'keygoals'                              => $keyGoalReview->keyGoals,
                'professionalDevelopmentPlanQuestion'   => $professionalDevelopmentPlanQuestion,
                'professionalDevelopmentPlan'           => $professionalDevelopmentPlan,
                'nextLineManagerExists'                 => $nextLineManagerExists,
                'receivers'                             => $receivers,
                'newKeyGoals' => $staffClearance->keyGoals()->where('type', 'current')->get()
            ];

            return view('ExitStaffClearance::Review.MidTermPerformanceReview.create', $record, $array);
        } else {
            $array = [
                'performanceReview'                     => $staffClearance,
                'professionalDevelopmentPlanQuestion'   => $this->clearanceDepartments->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals'                       => $staffClearance->keyGoals->where('type', '=', 'current'),
                'nextLineManagerExists'                 => $nextLineManagerExists,
                'receivers'                             => $receivers
            ];

            return view('ExitStaffClearance::Review.KeyGoalsReview.create', $array);
        }
    }

    public function store(StoreRequest $request, $clearanceId)
    {
        $staffClearance = $this->staffClearance->find($clearanceId);
        $this->authorize('certify', $staffClearance);
        $inputs = $request->validated();

        $staffClearance = $this->staffClearance->certify($clearanceId, $inputs);

        if ($staffClearance) {

            $staffClearance->endorser->notify(new StaffClearanceCertified($staffClearance));

            return redirect()->route('staff.clearance.index')->withSuccessMessage('Staff Clearance successfully certified.');
        }

        return redirect()->back()->withErrorMessage('Staff Clearance Cannot be updated');

    }
}

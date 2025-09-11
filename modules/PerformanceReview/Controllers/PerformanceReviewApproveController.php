<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Models\PerformanceReviewLog;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\PerformanceReview\Notifications\PerformanceReviewApproved;
use Modules\PerformanceReview\Notifications\PerformanceReviewReturned;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\PerformanceReview\Requests\PerformanceReviewApprove\StoreRequest;
use Yajra\DataTables\DataTables;

class PerformanceReviewApproveController extends Controller
{
    public function __construct(
        protected PerformanceReviewRepository $performanceReview,
        protected PerformanceReviewLog $performanceReviewLog,
        protected PerformanceReviewQuestion $performanceReviewQuestion
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->performanceReview
                ->where('status_id', '=', config('constant.RECOMMENDED_STATUS'))
                ->where('approver_id', '=', $authUser->id)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($performanceReview) {
                    return $performanceReview->getEmployeeName();
                })
                ->addColumn('fiscal_year', function ($performanceReview) {
                    return $performanceReview->getFiscalYear();
                })
                ->addColumn('review_type', function ($performanceReview) {
                    return $performanceReview->getReviewType();
                })
                ->addColumn('review_from', function ($performanceReview) {
                    return $performanceReview->getReviewFromDate();
                })
                ->addColumn('review_to', function ($performanceReview) {
                    return $performanceReview->getReviewToDate();
                })
                ->addColumn('status', function ($performanceReview) {
                    return '<span class="'.$performanceReview->getStatusClass().'">'.$performanceReview->getStatus().'</span>';
                })
                ->addColumn('action', function ($performanceReview) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('performance.approve.create', [$performanceReview->id]).'" rel="tooltip" title="Fill/Approve Performance Review Form"><i class="bi bi-ui-checks"></i></a>';

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PerformanceReview::Approve.index');
    }

    public function create(Request $request, $id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('approve', $performanceReview);

        $record = [
            'performanceReview' => $performanceReview,
            'groupBQuestions' => $this->performanceReviewQuestion->where('group', 'B')->orderBy('position')->get(),
            'groupDQuestions' => $this->performanceReviewQuestion->where('group', 'D')->orderBy('position')->get(),
            'groupEQuestions' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position')->get(),
            'groupFQuestions' => $this->performanceReviewQuestion->where('group', 'F')->orderBy('position')->get(),
            'groupGQuestions' => $this->performanceReviewQuestion->where('group', 'G')->orderBy('position')->get(),
            'groupHQuestions' => $this->performanceReviewQuestion->where('group', 'H')->orderBy('position')->get(),
            'groupIQuestions' => $this->performanceReviewQuestion->where('group', 'I')->orderBy('position')->get(),
            'groupJQuestions' => $this->performanceReviewQuestion->where('group', 'J')->orderBy('position')->get(),
            'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'future'),
        ];

        if ($performanceReview->getReviewType() == 'Annual Review') {

            $midTermReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 2) //For mid-term review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3) //For key-goal review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            if ($midTermReview) {
                $keygoals = $keygoals->concat($midTermReview->keyGoals()->where('type', 'current')->get());
            }

            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::Approve.AnnualPerformanceReview.create', $record, compact('keyGoalReview', 'midTermReview', 'keygoals', 'professionalDevelopmentPlan'));

        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();
            $keygoals = $keyGoalReview->keyGoals;
            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            return view('PerformanceReview::Approve.MidTermPerformanceReview.create', $record, compact('keygoals', 'professionalDevelopmentPlan'));
        } else {
            return view('PerformanceReview::Approve.KeyGoalsReview.create', [
                'performanceReview' => $performanceReview,
                'professionalDevelopmentPlanQuestion' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            ]);
        }
    }

    public function store(StoreRequest $request)
    {
        $performanceReview = $this->performanceReview->find($request->performance_review_id);

        $this->authorize('approve', $performanceReview);

        $inputs = $request->validated();

        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $performanceReview = $this->performanceReview->approve($request->performance_review_id, $inputs);

        if ($performanceReview) {
            $message = '';
            if ($performanceReview->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Performance Review is successfully returned.';
                $performanceReview->requester->notify(new PerformanceReviewReturned($performanceReview));
            } else {
                $message = 'Performance Review is successfully approved.';
                $performanceReview->requester->notify(new PerformanceReviewApproved($performanceReview));
            }

            return redirect()->route('performance.approve.index')->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Performance review could not be approved.');
    }
}

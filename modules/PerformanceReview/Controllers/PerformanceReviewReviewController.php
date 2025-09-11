<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Models\PerformanceReviewLog;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\PerformanceReview\Notifications\PerformanceReviewApproved;
use Modules\PerformanceReview\Notifications\PerformanceReviewRecommended;
use Modules\PerformanceReview\Notifications\PerformanceReviewReturned;
use Modules\PerformanceReview\Notifications\PerformanceReviewVerified;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\PerformanceReview\Requests\PerformanceReviewReview\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class PerformanceReviewReviewController extends Controller
{
    public function __construct(
        protected PerformanceReviewRepository $performanceReview,
        protected PerformanceReviewLog $performanceReviewLog,
        protected PerformanceReviewQuestion $performanceReviewQuestion,
        protected UserRepository $user
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->performanceReview->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
                ->where('reviewer_id', '=', $authUser->id)
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
                    $btn .= route('performance.review.create', [$performanceReview->id]).'" rel="tooltip" title="Review Performance Review Form"><i class="bi bi-ui-checks"></i></a>';

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PerformanceReview::Review.index');
    }

    public function create(Request $request, $id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('review', $performanceReview);

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

        $nextLineManagerExists = $performanceReview->employee->getNextLineManagerUserId() == '' ? false : true;

        if ($nextLineManagerExists || $performanceReview->employee->latestTenure?->crossSuperVisor()->exists()) {
            $receivers = array_filter([$performanceReview->employee->latestTenure->nextLineManager->user, $performanceReview->employee->latestTenure->crossSupervisor?->user]);
        } else {
            $receivers = $this->user->permissionBasedUsers('approve-performance-review');
        }

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

            $array = [
                'midTermReview' => $midTermReview,
                'keyGoalReview' => $keyGoalReview,
                'keygoals' => $keygoals,
                'professionalDevelopmentPlanQuestion' => $professionalDevelopmentPlanQuestion,
                'professionalDevelopmentPlan' => $professionalDevelopmentPlan,
                'nextLineManagerExists' => $nextLineManagerExists,
                'receivers' => $receivers,
                'authUser' => auth()->user(),
            ];

            return view('PerformanceReview::Review.AnnualPerformanceReview.create', $record, $array);

        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();
            $professionalDevelopmentPlanQuestion = $this->performanceReviewQuestion->where('group', 'E')
                ->orderBy('position', 'desc')
                ->first();
            $professionalDevelopmentPlan = $keyGoalReview->getAnswer($professionalDevelopmentPlanQuestion->id);

            $array = [
                'keygoals' => $keyGoalReview->keyGoals,
                'professionalDevelopmentPlanQuestion' => $professionalDevelopmentPlanQuestion,
                'professionalDevelopmentPlan' => $professionalDevelopmentPlan,
                'nextLineManagerExists' => $nextLineManagerExists,
                'receivers' => $receivers,
                'newKeyGoals' => $performanceReview->keyGoals()->where('type', 'current')->get(),
            ];

            return view('PerformanceReview::Review.MidTermPerformanceReview.create', $record, $array);
        } else {
            $array = [
                'performanceReview' => $performanceReview,
                'professionalDevelopmentPlanQuestion' => $this->performanceReviewQuestion->where('group', 'E')->orderBy('position', 'desc')->first(),
                'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
                'nextLineManagerExists' => $nextLineManagerExists,
                'receivers' => $receivers,
            ];

            return view('PerformanceReview::Review.KeyGoalsReview.create', $array);
        }
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $performanceReview = $this->performanceReview->find($request->performance_review_id);

        $this->authorize('review', $performanceReview);

        // ED Approval
        if (($authUser->employee->designation_id == 9 || $authUser->can('approve-performance-review')) &&
            $request->status_id == config('constant.APPROVED_STATUS') &&
            $performanceReview->review_type_id == config('constant.ANNUAL_REVIEW')) {
            $inputs = $request->validated();
            $inputs['approver_id'] = $authUser->id;
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $performanceReview = $this->performanceReview->approve($request->performance_review_id, $inputs);
            if ($performanceReview) {
                $message = '';
                if ($performanceReview->status_id == config('constant.APPROVED_STATUS')) {
                    $message = 'Performance Review is successfully approved.';
                    $performanceReview->requester->notify(new PerformanceReviewApproved($performanceReview));
                } elseif ($performanceReview->status_id == config('constant.RETURNED_STATUS')) {
                    $message = 'Performance Review is successfully returned.';
                    $performanceReview->requester->notify(new PerformanceReviewReturned($performanceReview));
                }

                return redirect()->route('performance.review.index')->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()->withWarningMessage('Performance review could not be approved.');

        }

        if (in_array($performanceReview->review_type_id, [config('constant.KEY_GOALS_REVIEW'), config('constant.MID_TERM_REVIEW')])) {
            $inputs = $request->validated();
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $inputs['approver_id'] = auth()->user()->id;
            $performanceReview = $this->performanceReview->approve($request->performance_review_id, $inputs);
            if ($performanceReview) {
                $message = '';
                if ($performanceReview->status_id == config('constant.APPROVED_STATUS')) {
                    $message = 'Performance Review is successfully approved.';
                    $performanceReview->requester->notify(new PerformanceReviewApproved($performanceReview));
                } elseif ($performanceReview->status_id == config('constant.RETURNED_STATUS')) {
                    $message = 'Performance Review is successfully returned.';
                    $performanceReview->requester->notify(new PerformanceReviewReturned($performanceReview));
                }

                return redirect()->route('performance.review.index')->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()->withWarningMessage('Performance review could not be approved.');
        }

        $next_line_manager_id = $performanceReview->employee->getNextLineManagerUserId();

        if ($next_line_manager_id != '') {
            $inputs = $request->validated();
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            if ($request->status_id == config('constant.VERIFIED_STATUS')) {
                $inputs['recommender_id'] = $request->receiver_id;
            }
            $performanceReview = $this->performanceReview->verify($request->performance_review_id, $inputs);

            if ($performanceReview) {
                $message = '';
                if ($performanceReview->status_id == config('constant.RETURNED_STATUS')) {
                    $message = 'Performance Review is successfully returned.';
                    $performanceReview->requester->notify(new PerformanceReviewReturned($performanceReview));
                } else {
                    $message = 'Performance Review is successfully reviewed.';
                    $performanceReview->recommender->notify(new PerformanceReviewVerified($performanceReview));
                }

                return redirect()->route('performance.review.index')->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()->withWarningMessage('Performance review could not be reviewed.');
        } else {
            $inputs = $request->validated();
            $inputs['user_id'] = auth()->id();
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            if ($request->status_id == config('constant.VERIFIED_STATUS')) {
                $inputs['approver_id'] = $request->receiver_id;
            }
            $inputs['status_id'] = config('constant.RECOMMENDED_STATUS');
            $performanceReview = $this->performanceReview->verify($request->performance_review_id, $inputs);

            if ($performanceReview) {
                $message = '';
                if ($performanceReview->status_id == config('constant.RETURNED_STATUS')) {
                    $message = 'Performance Review is successfully returned.';
                    $performanceReview->requester->notify(new PerformanceReviewReturned($performanceReview));
                } else {
                    $message = 'Performance Review is successfully reviewed.';
                    $performanceReview->approver->notify(new PerformanceReviewRecommended($performanceReview));
                }

                return redirect()->route('performance.review.index')->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()->withWarningMessage('Performance review could not be reviewed.');
        }

    }
}

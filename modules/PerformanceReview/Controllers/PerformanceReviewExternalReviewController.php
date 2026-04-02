<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\PerformanceReview\Notifications\PerformanceReviewExternalReviewSubmitted;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Yajra\DataTables\DataTables;

class PerformanceReviewExternalReviewController extends Controller
{
    public function __construct(
        protected PerformanceReviewRepository $performanceReview
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->performanceReview
                ->where('external_reviewer_id', '=', $authUser->id)
                ->whereIn('review_type_id', [1, 2]) // Only Annual & Mid-Term
                ->with(['employee', 'fiscalYear', 'status', 'reviewType'])
                ->orderBy('created_at', 'desc')
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
                    return '<span class="' . $performanceReview->getStatusClass() . '">' .
                        $performanceReview->getStatus() . '</span>';
                })
                ->addColumn('action', function ($performanceReview) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="' .
                        route('performance.external-review.show', $performanceReview->id) .
                        '" rel="tooltip" title="View 360 Feedback"><i class="bi bi-eye"></i></a>';

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PerformanceReview::ExternalReview.index');
    }

    public function show($id)
    {
        $performanceReview = $this->performanceReview->find($id);

        $this->authorize('view', $performanceReview);

        $record = array(
            'performanceReview' => $performanceReview,
            'currentKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'current'),
            'futureKeyGoals' => $performanceReview->keyGoals->where('type', '=', 'future'),
        );

        if ($performanceReview->getReviewType() == 'Annual Review') {

            $midTermReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 2) //For mid-term review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3) //For key-goal review
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            if ($midTermReview) {
                $keygoals = $keygoals->concat($midTermReview->keyGoals()->where('type', 'current')->get());
            }

            return view('PerformanceReview::ExternalReview.AnnualPerformanceReview.show', [
                ...$record,
                'keyGoalReview' => $keyGoalReview,
                'midTermReview' => $midTermReview,
                'keygoals' => $keygoals,
                'performanceReview' => $performanceReview,
                'challenges' => $performanceReview->challenges,
                'coreCompetencies' => $performanceReview->coreCompetencies,
            ]);

        } elseif ($performanceReview->getReviewType() == 'Mid-Term Review') {
            $keyGoalReview = $this->performanceReview->where('fiscal_year_id', '=', $performanceReview->fiscal_year_id)
                ->where('review_type_id', '=', 3)
                ->where('employee_id', $performanceReview->employee_id)
                ->first();

            if (is_null($keyGoalReview)) {
                return redirect()->back()->withWarningMessage('Key-Goals not set yet.');
            }

            $keygoals = $keyGoalReview->keyGoals->where('type', 'current');
            $keygoals = $keygoals->concat($performanceReview->keyGoals()->where('type', 'current')->get());

            return view('PerformanceReview::ExternalReview.MidTermPerformanceReview.show', [
                ...$record,
                'keyGoalReview' => $keyGoalReview,
                'keygoals' => $keygoals,
                'performanceReview' => $performanceReview,
                'challenges' => $performanceReview->challenges,
                'coreCompetencies' => $performanceReview->coreCompetencies,
            ]);

        }
    }

    public function storeExternalReviewerComments(Request $request)
    {
        $request->validate([
            'performance_review_id' => 'required|exists:performance_reviews,id',
            'external_reviewer_comments' => 'required|string',
        ]);

        $performanceReview = $this->performanceReview->find($request->performance_review_id);

        $data = [
            'external_reviewer_comments' => $request->external_reviewer_comments,
        ];

        $isSubmit = $request->boolean('is_submit');
        if ($isSubmit) {
            $data['status_id'] = config('constant.CLOSED_STATUS');
        }

        $performanceReview->update($data);

        if ($isSubmit) {
            $performanceReview->logs()->create([
                'user_id' => auth()->id(),
                'original_user_id' => session()->has('original_user')
                    ? session()->get('original_user')
                    : null,
                'log_remarks' => '360 Feedback submitted and review closed.',
                'status_id' => config('constant.CLOSED_STATUS'),
            ]);
            if ($performanceReview->requester) {
                $performanceReview->requester->notify(new PerformanceReviewExternalReviewSubmitted($performanceReview));
            }

            if ($performanceReview->reviewer) {
                $performanceReview->reviewer->notify(new PerformanceReviewExternalReviewSubmitted($performanceReview));
            }
        }

        $message = $isSubmit ? 'Review submitted and closed successfully.' : 'Reviewer comments saved successfully.';

        return response()->json([
            'type' => 'success',
            'message' => $message
        ]);
    }
}
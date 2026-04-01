<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Models\PerformanceReview;
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
        $performanceReview = PerformanceReview::with([
            'employee.latestTenure',
            'fiscalYear',
            'status',
            'reviewType',
            'keyGoals',
            'challenges',
            'coreCompetencies',
            'developmentPlans',
            'logs.createdBy'
        ])->findOrFail($id);

        // if ($performanceReview->external_reviewer_id != auth()->id() && 
        //     !auth()->user()->can('manage-performance-review')) {
        //     abort(403);
        // }

        return view('PerformanceReview::ExternalReview.AnnualPerformanceReview.show', compact('performanceReview'));
    }
}
<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Yajra\DataTables\Facades\DataTables;

class PerformanceReviewSummaryController extends Controller
{
    public function __construct(
        protected PerformanceReviewRepository $performanceReviews
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->performanceReviews->getPerformanceReviewSummary();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('performance.summary.show', [
                        'fiscalYear' => $row->fiscal_year_id,
                        'reviewType' => $row->review_type_id
                    ]) . '" rel="tooltip" title="View Details">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    return $btn;
                })
                ->editColumn('not_submitted', fn($row) => $row->not_submitted ?? 0)
                ->editColumn('submitted', fn($row) => $row->submitted ?? 0)
                ->editColumn('approved', fn($row) => $row->approved ?? 0)
                ->editColumn('returned', fn($row) => $row->returned ?? 0)
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('PerformanceReview::PerformanceReviewSummary.index');
    }

    public function show(Request $request, $fiscalYear, $reviewType)
    {
        $performanceReviews = $this->performanceReviews->getPerformanceReviewsByFiscalYearAndType($fiscalYear, $reviewType);

        if ($request->ajax()) {
            return DataTables::of($performanceReviews)
                ->addIndexColumn()
                ->addColumn('employee_name', fn($row) => $row->getEmployeeName() ?? '-')
                ->addColumn('status_badge', function ($row) {
                    $statusId = $row->status_id;

                    $mapping = [
                        config('constant.CREATED_STATUS') => ['class' => 'bg-secondary', 'text' => 'Not Submitted'],
                        config('constant.RETURNED_STATUS') => ['class' => 'bg-danger', 'text' => 'Returned'],
                        config('constant.SUBMITTED_STATUS') => ['class' => 'bg-warning', 'text' => 'Submitted'],
                        config('constant.APPROVED_STATUS') => ['class' => 'bg-success', 'text' => 'Approved'],
                    ];

                    $info = $mapping[$statusId] ?? ['class' => 'bg-secondary', 'text' => 'Unknown'];

                    return "<span class='badge {$info['class']}'>{$info['text']}</span>";
                })
                ->addColumn('action', function ($row) {
                    $url = route('performance.show', $row->id);

                    return '<a class="btn btn-outline-primary btn-sm" href="' . $url . '" 
                               rel="tooltip" title="View Performance Review">
                               <i class="bi bi-eye"></i>
                            </a>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $fiscalYearTitle = $performanceReviews->first()?->fiscalYear?->getFiscalYear() ?? '-';
        $reviewTypeTitle = $performanceReviews->first()?->reviewType?->title ?? '-';

        return view('PerformanceReview::PerformanceReviewSummary.show', compact(
            'fiscalYear',
            'reviewType',
            'fiscalYearTitle',
            'reviewTypeTitle',
            'performanceReviews'
        ));
    }
}

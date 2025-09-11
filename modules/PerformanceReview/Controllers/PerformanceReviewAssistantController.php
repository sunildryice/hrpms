<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\PerformanceReview\Models\PerformanceReviewType;
use Modules\PerformanceReview\Notifications\PerformanceReviewCreated;
use Modules\PerformanceReview\Notifications\PerformanceReviewSubmitted;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\PerformanceReview\Requests\PerformanceReview\StoreRequest;
use Modules\PerformanceReview\Requests\PerformanceReview\UpdateRequest;
use Yajra\DataTables\DataTables;

class PerformanceReviewAssistantController extends Controller
{

    public function __construct(
        EmployeeRepository $employee,
        PerformanceReviewRepository $performanceReview,
        PerformanceReviewType $performanceReviewType,
        PerformanceReviewQuestion $performanceReviewQuestion,
        FiscalYearRepository $fiscalYear
    )
    {
        $this->employee                     = $employee;
        $this->performanceReview            = $performanceReview;
        $this->performanceReviewType        = $performanceReviewType;
        $this->performanceReviewQuestion    = $performanceReviewQuestion;
        $this->fiscalYear                   = $fiscalYear;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $employeeIds = [];
            if($authUser->employee_id) {
                $employeeIds = $this->employee->select(['id', 'supervisor_id', 'cross_supervisor_id', 'next_line_manager_id'])
                    ->where('supervisor_id', $authUser->employee_id)
                    ->orWhere('cross_supervisor_id', $authUser->employee_id)
                    ->orWhere('next_line_manager_id', $authUser->employee_id)
                    ->pluck('id')->toArray();
            }

            $data = $this->performanceReview->with(['employee', 'fiscalYear', 'status', 'reviewType'])
                ->whereIn('employee_id', $employeeIds)
                ->orderBy('created_at', 'desc')->get();

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
                ->addColumn('deadline_date', function ($performanceReview) {
                    return $performanceReview->getDeadlineDate();
                })
                ->addColumn('status', function ($performanceReview) {
                    return '<span class="' . $performanceReview->getStatusClass() . '">' . $performanceReview->getStatus() . '</span>';
                })
                ->addColumn('action', function ($performanceReview) use($authUser) {
                    $btn = '';
                    if ($authUser->can('view', $performanceReview)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.show', [$performanceReview->id]).'" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PerformanceReview::Assistant.index');
    }
}

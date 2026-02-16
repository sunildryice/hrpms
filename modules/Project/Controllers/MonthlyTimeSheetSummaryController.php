<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Privilege\Repositories\UserRepository;

class MonthlyTimeSheetSummaryController extends Controller
{
    public function __construct(
        protected TimeSheetRepository $timeSheets,
        protected UserRepository $userRepo
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->timeSheets->getMonthlySummary();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('monthly-timesheet.summary.show', [
                        'year' => $row->year,
                        'month' => $row->month
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

        return view('Project::MonthlyTimeSheetSummary.index');
    }

    public function show(Request $request, $year, $month)
    {
        $timesheets = $this->timeSheets->getTimesheetsByYearAndMonth($year, $month);
        if ($request->ajax()) {

            return DataTables::of($timesheets)
                ->addIndexColumn()
                ->addColumn('requester_name', fn($row) => $row->requester?->getFullName() ?? '-')
                ->addColumn('status_name', function ($row) {
                    if ($row->status_id == config('constant.CREATED_STATUS')) {
                        return 'Not Submitted';
                    }
                    return $row->getStatus() ?? 'Unknown';
                })
                ->addColumn('status_badge', function ($row) {
                    $statusId = $row->status_id;

                    $mapping = [
                        config('constant.CREATED_STATUS') => ['class' => 'bg-secondary', 'text' => 'Not Submitted'],
                        config('constant.SUBMITTED_STATUS') => ['class' => 'bg-warning', 'text' => 'Submitted'],
                        config('constant.APPROVED_STATUS') => ['class' => 'bg-success', 'text' => 'Approved'],
                        config('constant.RETURNED_STATUS') => ['class' => 'bg-danger', 'text' => 'Returned'],
                    ];

                    $info = $mapping[$statusId] ?? ['class' => 'bg-secondary', 'text' => 'Unknown'];

                    return "<span class='badge {$info['class']}'>{$info['text']}</span>";
                })
                ->addColumn('approved_by', function ($row) {
                    if ($row->isApproved()) {
                        return $row->approvedLog?->user?->getFullName()
                            ?? $row->approver?->getFullName()
                            ?? '-';
                    }
                    return '-';
                })
                ->rawColumns(['status_badge'])
                ->make(true);
        }

        return view('Project::MonthlyTimeSheetSummary.show', compact('year', 'month', 'timesheets'));
    }
}
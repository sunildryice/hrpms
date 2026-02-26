<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ViewUserTimeSheetRepository;
use Yajra\DataTables\Facades\DataTables;

class MonthlyTimeSheetSummaryController extends Controller
{
    public function __construct(
        protected TimeSheetRepository $timeSheets,
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected ViewUserTimeSheetRepository $viewUserTimeSheets,
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
                ->addColumn('action', function ($row) use ($year, $month) {
                    $url = route('monthly-timesheet.summary.employee.show', [
                        'year' => $year,
                        'month' => $month,
                        'timesheet' => $row->id,
                    ]);

                    return '<a class="btn btn-outline-primary btn-sm" href="' . $url . '" 
                               rel="tooltip" title="View Monthly Timesheet">
                               <i class="bi bi-eye"></i>
                            </a>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('Project::MonthlyTimeSheetSummary.show', compact('year', 'month', 'timesheets'));
    }

    public function showMonthlyTimesheet($year, $month, $timesheet)
    {
        $monthlyTimeSheet = $this->timeSheets->find($timesheet);

        if ($monthlyTimeSheet->year != $year || $monthlyTimeSheet->month != $month) {
            abort(404);
        }

        $employee = $monthlyTimeSheet->requester;

        $activityTimeSheets = $this->activityTimeSheets->getTimeSheetsByPeriod(
            $monthlyTimeSheet->start_date,
            $monthlyTimeSheet->end_date,
            $employee->id
        );

        $grouped = $activityTimeSheets->groupBy(
            fn($item) => $item->timesheet_date->format('Y-m-d')
        );

        $start = \Carbon\Carbon::parse($monthlyTimeSheet->start_date);
        $end = \Carbon\Carbon::parse($monthlyTimeSheet->end_date);

        $allDates = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateKey = $current->format('Y-m-d');
            $items = $grouped->get($dateKey, collect());

            $reason = $items->isEmpty()
                ? $this->viewUserTimeSheets->getAbsenceReason($employee->id, $dateKey)
                : null;

            $allDates[$dateKey] = [
                'items' => $items,
                'reason' => $reason,
                'date' => $dateKey,
                'carbon' => $current->copy(),
            ];

            $current->addDay();
        }

        $yearMonth = $monthlyTimeSheet->month_name . ' ' . $monthlyTimeSheet->year;

        return view('Project::MonthlyTimeSheetSummary.view', compact(
            'allDates',
            'yearMonth',
            'monthlyTimeSheet',
            'employee',
            'year',
            'month'
        ));
    }
}
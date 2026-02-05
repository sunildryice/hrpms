<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\TimeSheet;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Privilege\Repositories\UserRepository;

class MonthlyTimeSheetApprovedController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected TimeSheetRepository $timeSheets,
        protected UserRepository $userRepository
    ) {
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if ($request->ajax()) {
            $query = $this->activityTimeSheets->getApprovedMonthlyTimeSheets($authUser->id);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('month_year', fn($row) => $row->month . ' ' . $row->year)
                ->addColumn(
                    'period',
                    fn($row) =>
                    Carbon::parse($row->start_date)->format('d M') . ' — ' .
                    Carbon::parse($row->end_date)->format('d M Y')
                )
                ->addColumn('requester', fn($row) => $row->requester?->getFullName() ?? '-')
                ->addColumn('total_hours', fn($row) => number_format($row->total_hours ?? 0, 2) . ' hrs')
                ->addColumn(
                    'approved_at',
                    fn($row) =>
                    $row->approved_at
                    ? Carbon::parse($row->approved_at)->format('d M Y • H:i')
                    : '-'
                )
                ->addColumn('status', fn($row) => sprintf(
                    '<span class="badge bg-success">Approved</span>'
                ))
                ->addColumn('action', function ($row) {
                    return sprintf(
                        '<a href="%s" class="btn btn-sm btn-outline-info" title="View Approved Timesheet">
                            <i class="bi bi-eye"></i> View
                        </a>',
                        route('approved.monthly-timesheet.show', $row->id)
                    );
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('Project::MonthlyTimeSheetApproved.index');
    }

    public function show($id)
    {
        $authUser = Auth::user();

        $timeSheet = TimeSheet::query()
            ->where('id', $id)
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->with([
                'requester',
                'approver',
                'status',
                'logs' => fn($q) => $q->latest(),
            ])
            ->firstOrFail();

        $activities = $this->activityTimeSheets->getTimeSheetsByPeriod(
            $timeSheet->start_date,
            $timeSheet->end_date,
            $timeSheet->requester_id
        );

        $groupedActivities = $activities->groupBy(fn($item) => $item->timesheet_date);

        $calendar = [];
        $currentDate = Carbon::parse($timeSheet->start_date);
        $endDate = Carbon::parse($timeSheet->end_date);

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $calendar[$dateKey] = $groupedActivities->get($dateKey, collect());
            $currentDate->addDay();
        }

        $stats = [
            'total_hours' => (float) $activities->sum('hours_spent'),
            'project_count' => $activities->pluck('project_id')->filter()->unique()->count(),
            'activity_count' => $activities->pluck('activity_id')->filter()->unique()->count(),
            'days_recorded' => $activities->groupBy('timesheet_date')->count(),
            'approved_by' => $timeSheet->approver?->getFullName(),
            'approved_at' => $timeSheet->approved_at ? Carbon::parse($timeSheet->approved_at)->format('d M Y H:i') : null,
        ];

        return view('Project::MonthlyTimeSheetApproved.show', compact(
            'timeSheet',
            'calendar',
            'stats',
            'activities',
            'authUser'
        ));
    }
}
<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Project\Models\TimeSheet;
use Yajra\DataTables\Facades\DataTables;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
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
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = TimeSheet::query()
                ->from('view_user_timesheets as v')
                // ->where('v.approver_id', $authUser->id)
                ->where('v.status_id', config('constant.APPROVED_STATUS'));

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('month_name', function ($row) {
                    return $row->month_name;
                })
                ->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('projects', function ($row) {
                    $projectShortNames = explode(', ', $row->project_short_names ?? '');
                    $badges = '';
                    foreach ($projectShortNames as $shortName) {
                        $badges .= '<span class="badge bg-info text-dark me-1 mb-1">' . trim($shortName) . '</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $url = route('approved.monthly-timesheet.show', $row->id);
                    return '<a class="btn btn-outline-primary btn-sm" href="' . $url . '" title="Approved Monthly TimeSheet">
                <i class="bi bi-eye"></i>
            </a>';
                })
                ->rawColumns(['action', 'projects', 'status'])
                ->make(true);
        }

        return view('Project::MonthlyTimeSheet.Approved.index');
    }

    public function show($id)
    {
       $authUser = auth()->user();

        $timeSheet = TimeSheet::where('id', $id)
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->with(['requester', 'approver', 'status', 'logs' => fn($q) => $q->latest(),])
            ->firstOrFail();

        $timeSheets = $this->activityTimeSheets->getTimeSheetsByPeriod(
            $timeSheet->start_date,
            $timeSheet->end_date,
            $timeSheet->requester_id
        );
        $yearMonthFormatted = $timeSheet->month . ' ' . $timeSheet->year;

        $startDate = \Carbon\Carbon::parse($timeSheet->start_date);
        $endDate = \Carbon\Carbon::parse($timeSheet->end_date);

        $groupedTimeSheets = $timeSheets->groupBy(function ($ts) {
            return \Carbon\Carbon::parse($ts->timesheet_date)->format('Y-m-d');
        });

        $allDates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $allDates[$dateKey] = $groupedTimeSheets->get($dateKey, collect([]));
            $currentDate->addDay();
        }

        $stats = [
            'projects' => $timeSheets->pluck('project_id')->filter()->unique()->count(),
            'activities' => $timeSheets->pluck('activity_id')->filter()->unique()->count(),
            'tasks' => $timeSheets->count(),
            'hours' => (float) $timeSheets->sum('hours_spent'),
        ];

        return view('Project::MonthlyTimeSheet.Approved.show', compact(
            'allDates',
            'yearMonthFormatted',
            'stats',
            'timeSheet',
            'authUser'
        ));
    }
}
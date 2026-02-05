<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Project\Models\TimeSheet;
use Modules\Project\Models\TimeSheetLog;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Notifications\TimeSheetSubmitted;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class MonthlyTimeSheetApproverController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected TimeSheetRepository $timeSheets,
        protected UserRepository $user

    ) {
    }
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->activityTimeSheets->getApproverMonthlyTimeSheets($authUser->id);
            
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
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('monthly-timesheet.show', $row->month) . '" rel="tooltip" title="View Timesheet Details">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'projects', 'status'])
                ->make(true);
        }
        return view('Project::MonthlyTimeSheetApprover.index');
    }


    public function show($yearMonth)
    {
        $authUser = auth()->user();
        [$year, $monthNum] = explode('-', $yearMonth);

        $timeSheet = TimeSheet::where('year', $year)
            ->whereRaw('MONTH(start_date) = ?', [(int) $monthNum])
            ->where('requester_id', auth()->id())
            ->firstOrFail();

        $timeSheets = $this->activityTimeSheets->getTimeSheetsByPeriod($timeSheet->start_date, $timeSheet->end_date, auth()->id());
        $yearMonthFormatted = $timeSheet->month . ' ' . $timeSheet->year;
        // Generate all dates for the period
        $startDate = \Carbon\Carbon::parse($timeSheet->start_date);
        $endDate = \Carbon\Carbon::parse($timeSheet->end_date);
        // Group timesheets by date
        $groupedTimeSheets = $timeSheets->groupBy(function ($ts) {
            return \Carbon\Carbon::parse($ts->timesheet_date)->format('Y-m-d');
        });
        // Create array of all dates with their timesheets
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
        $supervisors = $this->user->getSupervisors($authUser);
        return view('Project::MonthlyTimeSheet.show', compact('allDates', 'yearMonthFormatted', 'yearMonth', 'stats', 'timeSheet', 'supervisors', 'authUser'));
    }

    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $travelReport = $this->travelReport->find($id);
        $this->authorize('approve', $travelReport);

        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $travelReport = $this->travelReport->approve($travelReport->id, $inputs);

        if ($travelReport) {
            $message = '';
            if ($travelReport->status_id == 2) {
                $message = 'Travel report is successfully returned.';
                $travelReport->reporter->notify(new TravelReportReturned($travelReport));
            } else {
                $message = 'Travel report is successfully approved.';
                $travelReport->reporter->notify(new TravelReportApproved($travelReport));
            }

            return redirect()->route('approve.travel.reports.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Travel Report can not be approved.');
    }
}

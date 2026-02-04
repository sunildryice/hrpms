<?php
namespace Modules\Project\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Project\Models\TimeSheet;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class MonthlyTimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected TimeSheetRepository $timeSheets,
        protected UserRepository $user

    ) {
        $this->destinationPath = 'TimeSheet';
    }
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->activityTimeSheets
                ->getMonthlyTimeSheets($authUser->id);
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
        return view('Project::MonthlyTimeSheet.index');
    }
    public function show($yearMonth)
    {
        $authUser = auth()->user();
        [$year, $monthNum] = explode('-', $yearMonth);

        $timeSheet = \Modules\Project\Models\TimeSheet::where('year', $year)
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
        return view('Project::MonthlyTimeSheet.show', compact('allDates', 'yearMonthFormatted', 'yearMonth', 'stats', 'timeSheet', 'supervisors'));
    }

    public function update(Request $request, $id)
    {
        $timeSheet = TimeSheet::where('id', $id)
            ->where('requester_id', auth()->id())
            ->firstOrFail();

        // Only allow update if period has ended
        if (now()->lte($timeSheet->end_date)) {
            return redirect()->back()
                ->with('error', 'You can only submit for approval after the timesheet period ends.');
        }

        $validated = $request->validate([
            'approver_id' => 'required|exists:users,id',
        ]);

        if ($timeSheet->isApproved()) {
            return redirect()->back()
                ->with('warning', 'This timesheet is already approved.');
        }

        $timeSheet->update([
            'approver_id' => $validated['approver_id'],
            'status_id' => config('constant.PENDING_STATUS') ?? $timeSheet->status_id,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('monthly-timesheet.index')
            ->with('success', 'Timesheet has been successfully submitted to the approver.');
    }
}
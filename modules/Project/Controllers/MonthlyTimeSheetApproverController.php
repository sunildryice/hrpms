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
use Modules\Project\Notifications\TimeSheetApproved;
use Modules\Project\Notifications\TimeSheetReturned;
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
                    $url = route('approve.monthly-timesheet.create', $row->id);
                    return '<a class="btn btn-outline-primary btn-sm" href="' . $url . '" title="Approve Monthly TimeSheet">
                <i class="bi bi-box-arrow-in-up-right"></i>
            </a>';
                })
                ->rawColumns(['action', 'projects', 'status'])
                ->make(true);
        }
        return view('Project::MonthlyTimeSheetApprover.index');
    }


    public function create($id)
    {
        $authUser = auth()->user();

        $timeSheet = TimeSheet::where('id', $id)
            ->where('approver_id', $authUser->id)
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->with(['requester', 'status', 'logs'])
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

        return view('Project::MonthlyTimeSheetApprover.create', compact(
            'allDates',
            'yearMonthFormatted',
            'stats',
            'timeSheet',
            'authUser'
        ));
    }

    public function store(Request $request, $id)
    {
        $authUser = auth()->user();
        $timeSheet = TimeSheet::where('id', $id)
            ->where('approver_id', $authUser->id)
            ->where('status_id', config('constant.SUBMITTED_STATUS'))
            ->firstOrFail();

        // Validate form input
        $validated = $request->validate([
            'status_id' => 'required|in:2,6',           // 2 = Return, 6 = Approve
            'log_remarks' => 'nullable|string|max:1000',
            'btn' => 'required|in:submit',
        ]);

        $newStatusId = (int) $validated['status_id'];
        $remarks = trim($validated['log_remarks'] ?? '');

        // Define outcome based on selected status
        $isApproved = $newStatusId === config('constant.APPROVED_STATUS');
        $message = $isApproved ? 'Timesheet has been approved.' : 'Timesheet has been returned to the requester.';

        DB::beginTransaction();

        try {
            // Update timesheet status
            $timeSheet->update([
                'status_id' => $newStatusId,
                'updated_by' => $authUser->id,
                'updated_at' => now(),
            ]);

            TimeSheetLog::create([
                'timesheet_id' => $timeSheet->id,
                'user_id' => $authUser->id,
                'status_id' => $newStatusId,
                'log_remarks' => $remarks ?: ($isApproved ? 'Approved' : 'Returned') . ' by ' . $authUser->getFullName(),
            ]);

            if ($timeSheet->requester) {
                if ($isApproved) {
                    $timeSheet->requester->notify(new TimeSheetApproved($timeSheet));
                } else {
                    $timeSheet->requester->notify(new TimeSheetReturned($timeSheet));
                }
            }

            DB::commit();

            return redirect()->route('approve.monthly-timesheet.index')->with('success_message', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Timesheet approval/return failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error_message', 'Failed to process your action. Please try again.');
        }
    }
}

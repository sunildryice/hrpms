<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\TimeSheet;
use Modules\Project\Models\TimeSheetLog;
use Modules\Project\Notifications\TimeSheetApproved;
use Modules\Project\Notifications\TimeSheetReturned;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ViewUserTimeSheetRepository;
use Yajra\DataTables\Facades\DataTables;

class MonthlyTimeSheetApproverController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected ViewUserTimeSheetRepository $viewUserTimeSheets,
        protected TimeSheetRepository $timeSheets,
        protected UserRepository $user

    ) {
    }
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->viewUserTimeSheets->getApproverTimeSheets($authUser->id);

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
        return view('Project::MonthlyTimeSheet.Approver.index');
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

        $startDate = Carbon::parse($timeSheet->start_date);
        $endDate = Carbon::parse($timeSheet->end_date);

        $groupedTimeSheets = $timeSheets->groupBy(function ($ts) {
            return Carbon::parse($ts->timesheet_date)->format('Y-m-d');
        });

        $allDates = [];
        $currentDate = $startDate->copy();
        $employeeId = $timeSheet->requester_id;

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $items = $groupedTimeSheets->get($dateKey, collect([]));

            $reason = $items->isEmpty()
                ? $this->viewUserTimeSheets->getAbsenceReason($employeeId, $dateKey)
                : null;

            $allDates[$dateKey] = [
                'items' => $items,
                'reason' => $reason,
                'date' => $dateKey,
                'carbon' => $currentDate->copy(),
            ];

            $currentDate->addDay();
        }

        $stats = [
            'projects' => $timeSheets->pluck('project_id')->filter()->unique()->count(),
            'activities' => $timeSheets->pluck('activity_id')->filter()->unique()->count(),
            'tasks' => $timeSheets->count(),
            'hours' => (float) $timeSheets->sum('hours_spent'),
        ];

        return view('Project::MonthlyTimeSheet.Approver.create', compact(
            'allDates',
            'yearMonthFormatted',
            'stats',
            'timeSheet',
            'authUser',
            'employeeId'
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

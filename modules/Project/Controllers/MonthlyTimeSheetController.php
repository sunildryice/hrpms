<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\TimeSheet;
use Modules\Project\Models\TimeSheetLog;
use Modules\Project\Notifications\TimeSheetSubmitted;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Repositories\TimeSheetRepository;
use Modules\Project\Repositories\ViewUserTimeSheetRepository;
use Yajra\DataTables\Facades\DataTables;

class MonthlyTimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets,
        protected TimeSheetRepository $timeSheets,
        protected ViewUserTimeSheetRepository $viewUserTimeSheets,
        protected UserRepository $user
    ) {
        $this->destinationPath = 'TimeSheet';
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->viewUserTimeSheets->getUserTimeSheets($authUser->id);

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
                    $btn .= route('monthly-timesheet.show', $row->id) . '" rel="tooltip" title="View Timesheet Details">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'projects', 'status'])
                ->make(true);
        }

        return view('Project::MonthlyTimeSheet.index');
    }

    public function show($id)
    {
        $authUser = auth()->user();

        $timeSheet = TimeSheet::where('id', $id)
            ->where('requester_id', $authUser->id)
            ->firstOrFail();

        $timeSheets = $this->activityTimeSheets->getTimeSheetsByPeriod(
            $timeSheet->start_date,
            $timeSheet->end_date,
            auth()->id()
        );

        $yearMonthFormatted = $timeSheet->month . ' ' . $timeSheet->year;

        $startDate = Carbon::parse($timeSheet->start_date);
        $endDate = Carbon::parse($timeSheet->end_date);

        $groupedTimeSheets = $timeSheets->groupBy(
            fn($ts) =>
            Carbon::parse($ts->timesheet_date)->format('Y-m-d')
        );

        $allDates = [];
        $currentDate = $startDate->copy();
        $employeeId = $timeSheet->requester_id;

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');

            $items = $groupedTimeSheets->get($dateKey, collect([]));

            // If empty → compute reason once here
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

        $supervisors = $this->user->getSupervisors($authUser);

        return view('Project::MonthlyTimeSheet.show', compact(
            'allDates',
            'yearMonthFormatted',
            'stats',
            'timeSheet',
            'supervisors',
            'authUser',
            'employeeId'
        ));
    }

    public function update(Request $request, $id)
    {
        $authUser = auth()->user();

        $timeSheet = TimeSheet::where('id', $id)
            ->where('requester_id', $authUser->id)
            ->firstOrFail();

        $inputs = $request->validate([
            'approver_id' => 'required|exists:users,id',
            'action' => 'required|in:submit',
        ]);

        // Prevent re-submission if already submitted or approved
        if (
            in_array($timeSheet->status_id, [
                config('constant.SUBMITTED_STATUS'),
                config('constant.APPROVED_STATUS'),
            ])
        ) {
            return redirect()->back()->with('warning_message', 'This timesheet has already been submitted or approved.');
        }

        DB::beginTransaction();

        try {
            $timeSheet->update([
                'approver_id' => $inputs['approver_id'],
                'updated_by' => $authUser->id,
                'updated_at' => now(),
            ]);

            if ($request->input('action') === 'submit') {
                $newStatus = config('constant.SUBMITTED_STATUS');

                $timeSheet->update([
                    'status_id' => $newStatus,
                ]);

                TimeSheetLog::create([
                    'timesheet_id' => $timeSheet->id,
                    'user_id' => $authUser->id,
                    'log_remarks' => 'Timesheet submitted for approval.',
                    'status_id' => $newStatus,
                ]);

                if ($timeSheet->approver) {
                    $timeSheet->approver->notify(new TimeSheetSubmitted($timeSheet));
                }
            }

            DB::commit();

            return redirect()
                ->route('monthly-timesheet.index')
                ->with('success_message', 'Timesheet has been successfully submitted for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Timesheet submission failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error_message', 'Failed to submit timesheet. Please try again.');
        }
    }
}
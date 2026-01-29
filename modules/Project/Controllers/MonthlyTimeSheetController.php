<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class MonthlyTimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets
    ) {
        $this->destinationPath = 'TimeSheet';
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->timeSheets
                ->getMonthlyTimeSheets($authUser->id);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('month_name', function ($row) {
                    return $row->month_name;
                })
                ->addColumn('projects', function ($row) {
                    $projectShortNames = explode(',', $row->project_short_names);
                    $badges = '';
                    foreach ($projectShortNames as $shortName) {
                        $badges .= '<span class="badge bg-info text-dark me-1 mb-1">' . trim($shortName) . '</span>';
                    }
                    return $badges;
                })
                ->addColumn('action', function ($row) use ($authUser) {

                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .=  route('monthly-timesheet.show', $row->month) . '" rel="tooltip" title="View Timesheet Details">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    // $btn .= ' <a class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    // $btn .= route('activity-stage.edit', $row->id) . '" rel="tooltip" title="Edit Activity Stage">';
                    // $btn .= '<i class="bi bi-pencil-square"></i></a>';

                    // $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"';
                    // $btn .= ' data-href="' . route('activity-stages.destroy', $row->id) . '" rel="tooltip"';
                    // $btn .= ' title="Delete Activity Stage"><i class="bi bi-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'projects'])
                ->make(true);
        }


        return view('Project::MonthlyTimeSheet.index');
    }

    public function show($yearMonth)
    {
        $timeSheets = $this->timeSheets->getTimeSheetsByMonth($yearMonth, auth()->id());

        $yearMonthFormatted = date('F Y', strtotime($yearMonth . '-01'));

        // Generate all dates for the month
        $startDate = \Carbon\Carbon::parse($yearMonth . '-01')->startOfMonth();
        $endDate = \Carbon\Carbon::parse($yearMonth . '-01')->endOfMonth();

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

        return view('Project::MonthlyTimeSheet.show', compact('allDates', 'yearMonthFormatted', 'yearMonth', 'stats'));
    }
}

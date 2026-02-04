<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\TimeSheet\StoreRequest;
use Modules\Project\Requests\TimeSheet\UpdateRequest;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Carbon\Carbon;

class WeeklyPlanController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
    ) {}

    public function index(Request $request)
    {
        $weeks = [];
        $date = Carbon::now()->startOfYear();
        $limitDate = Carbon::now()->addMonth();
        $weekCount = 1;

        while ($date <= $limitDate) {
            $weekStart = $date->copy();
            $endOfCurrentYear = $date->copy()->endOfYear();

            // Calculate end of week (Saturday)
            // Carbon dayOfWeek: 0 (Sunday) - 6 (Saturday)
            $daysToSaturday = 6 - $weekStart->dayOfWeek;
            $weekEnd = $weekStart->copy()->addDays($daysToSaturday);

            // Cap at end of current year
            if ($weekEnd > $endOfCurrentYear) {
                $weekEnd = $endOfCurrentYear;
            }

            // Check duration (inclusive)
            $duration = $weekStart->diffInDays($weekEnd) + 1;

            // user requested to skip weeks starting with Thursday or having 2-3 days
            // If week starts on Thursday (and ends Saturday), it has 3 days.
            // If week has <= 3 days, we skip it.
            if ($duration > 3) {
                $now = Carbon::now()->startOfDay();
                $isCurrentWeek = $now->between($weekStart, $weekEnd);
                $isPastWeek = $weekEnd->lt($now);

                $weeks[] = [
                    'label' => 'Week ' . $weekCount++,
                    'start_date' => $weekStart->format('M j, Y'),
                    'end_date' => $weekEnd->format('M j, Y'),
                    'start_date_raw' => $weekStart->format('Y-m-d'),
                    'end_date_raw' => $weekEnd->format('Y-m-d'),
                    'is_current' => $isCurrentWeek,
                    'is_past' => $isPastWeek,
                ];
            }

            $date = $weekEnd->copy()->addDay();
        }

        return view('Project::WeeklyPlan.index', compact('weeks'));
    }

    public function details(Request $request, $startOfWeek, $endOfWeek)
    {
        $startDate = Carbon::parse($startOfWeek);
        $endDate = Carbon::parse($endOfWeek);

        return view('Project::WeeklyPlan.Detail.index', [
            'week' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}

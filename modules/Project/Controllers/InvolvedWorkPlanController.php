<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Repositories\WorkPlanRepository;
use Modules\Project\Models\Enums\WorkPlanStatus;

class InvolvedWorkPlanController extends Controller
{
    public function __construct(protected WorkPlanRepository $workPlans) {}

    public function index(Request $request)
    {
        $userId = auth()->id();

        $weekSelection = $this->workPlans->getWeekSelectionForUser($userId, $request->get('week_start'));
        $weeks = $weekSelection['weeks'];
        $currentWeekStart = $weekSelection['current_week_start'];
        $currentWeekEnd = $weekSelection['current_week_end'];
        $selectedWeek = $weekSelection['selected_week'];

        if ($request->ajax()) {
            if (!$selectedWeek) {
                return DataTables::of(collect([]))->make(true);
            }

            $detailsQuery = $this->workPlans->getUserWorkPlanDetailsByWeek(
                $currentWeekStart->toDateString(),
                $currentWeekEnd->toDateString(),
                $userId
            );

            return DataTables::of($detailsQuery)
                ->addIndexColumn()
                ->addColumn('plan_tasks', fn($row) => $row->plan_tasks)
                ->addColumn('reason', fn($row) => $row->reason ?? '')
                ->editColumn('status', function ($row) {
                    $statusEnum = WorkPlanStatus::tryFrom($row->status) ?? WorkPlanStatus::NotStarted;
                    return '<span class="' . $statusEnum->colorClass() . '">' . $statusEnum->label() . '</span>';
                })
                ->addColumn('created_by', function ($row) {
                    return optional($row->workPlan->employee)->full_name ?? 'N/A';
                })
                ->addColumn('members_data', function ($row) use ($userId) {
                    return $row->members->map(function ($member) use ($userId) {
                        return [
                            'id' => $member->id,
                            'name' => $member->full_name ?? $member->name ?? 'N/A',
                            'is_self' => $member->id === $userId,
                        ];
                    })->values();
                })
                ->addColumn('work_plan_meta', function ($row) {
                    $workPlan = $row->workPlan;
                    return [
                        'from_date' => $workPlan?->from_date?->toDateString(),
                        'to_date' => $workPlan?->to_date?->toDateString(),
                    ];
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" class="btn btn-outline-primary btn-sm view-work-plan" data-detail-id="' . $row->id . '"><i class="bi bi-eye"></i></button>';
                })
                ->rawColumns(['status', 'action', 'documents'])
                ->make(true);
        }

        return view('Project::InvolvedWorkPlan.index', [
            'weeks' => $weeks,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd,
        ]);
    }
}

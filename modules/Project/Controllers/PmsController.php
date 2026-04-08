<?php

namespace Modules\Project\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Project;
use Modules\Project\Repositories\ProjectRepository;

class PmsController
{

    public function __construct(
        protected UserRepository $userRepository,
        protected ProjectRepository $projects,
        protected RoleRepository $roles,
    ) {
    }
    public function dashboard(Request $request)
    {
        $authUser = auth()->user()->load('roles');
        $isPMLT = $authUser->roles->contains('role', 'PMLT');

        $projectIds = $request->query('project_ids', []);
        $startDateFilter = $request->query('start_date');
        $endDateFilter = $request->query('end_date');

        $query = $this->projects->getModel()
            ->whereNotNull('activated_at')
            ->where('show_pms_dashboard', true);

        if (!$isPMLT) {
            $query->where(function ($q) use ($authUser) {
                $q->where('focal_person_id', $authUser->id)
                    ->orWhere('team_lead_id', $authUser->id)
                    ->orWhereHas('members', function ($sq) use ($authUser) {
                        $sq->where('user_id', $authUser->id);
                    });
            });
        }

        $query->withCount([
            'activities as completed_count' => function ($q) use ($startDateFilter, $endDateFilter) {
                $q->where('status', ActivityStatus::Completed)
                    ->where('activity_level', '!=', ActivityLevel::Theme->value);

                if ($startDateFilter && $endDateFilter) {
                    $q->whereBetween('completion_date', [$startDateFilter, $endDateFilter]);
                }
            },

            'activities as under_progress_count' => function ($q) use ($startDateFilter, $endDateFilter) {
                $q->where('status', ActivityStatus::UnderProgress)
                    ->where('activity_level', '!=', ActivityLevel::Theme->value);

                if ($startDateFilter && $endDateFilter) {
                    $q->whereBetween('completion_date', [$startDateFilter, $endDateFilter]);
                }
            },

            'activities as not_started_count' => function ($q) use ($startDateFilter, $endDateFilter) {
                $q->where('status', ActivityStatus::NotStarted)
                    ->where('activity_level', '!=', ActivityLevel::Theme->value);

                if ($startDateFilter && $endDateFilter) {
                    $q->whereBetween('completion_date', [$startDateFilter, $endDateFilter]);
                }
            },

            'activities as no_required_count' => function ($q) use ($startDateFilter, $endDateFilter) {
                $q->where('status', ActivityStatus::NoRequired)
                    ->where('activity_level', '!=', ActivityLevel::Theme->value);

                if ($startDateFilter && $endDateFilter) {
                    $q->whereBetween('completion_date', [$startDateFilter, $endDateFilter]);
                }
            },

            'activities as total_activities' => function ($q) use ($startDateFilter, $endDateFilter) {
                $q->where('activity_level', '!=', ActivityLevel::Theme->value);

                if ($startDateFilter && $endDateFilter) {
                    $q->whereBetween('completion_date', [$startDateFilter, $endDateFilter]);
                }
            },
        ]);

        // Filter by selected projects
        if (!empty($projectIds)) {
            $query->whereIn('id', $projectIds);
        }

        // Only show projects that have activities in selected date range
        if ($startDateFilter && $endDateFilter) {
            $query->whereHas('activities', function ($q) use ($startDateFilter, $endDateFilter) {
                $q->whereBetween('completion_date', [$startDateFilter, $endDateFilter])
                    ->where('activity_level', '!=', ActivityLevel::Theme->value);
            });
        }

        $projects = $query->orderBy('title')->get();

        $seriesTimeline = [
            ['name' => 'Completed', 'data' => []],
            ['name' => 'Under Progress', 'data' => []],
            ['name' => 'Not Started', 'data' => []],
            ['name' => 'Not Required', 'data' => []],
            ['name' => 'No Activities', 'data' => []],
        ];

        $projectNames = [];
        $minDate = null;
        $maxDate = null;

        foreach ($projects as $idx => $project) {
            $code = $project->short_name ?: 'P' . ($idx + 1);
            $projectNames[] = $code;

            // overall min/max for x-axis
            if ($project->start_date) {
                $minDate = $minDate ? min($minDate, $project->start_date) : $project->start_date;
            }
            if ($project->completion_date) {
                $maxDate = $maxDate ? max($maxDate, $project->completion_date) : $project->completion_date;
            }

            $start = $project->start_date ? Carbon::parse($project->start_date) : now()->subYears(2);
            $end = $project->completion_date ? Carbon::parse($project->completion_date) : now()->addYears(2);

            $totalActivities = max(1, $project->total_activities);

            $completed = $project->completed_count;
            $under = $project->under_progress_count;
            $notStarted = $project->not_started_count;
            $notRequired = $project->no_required_count;

            $hasAnyActivity = ($completed + $under + $notStarted + $notRequired) > 0;

            if (!$hasAnyActivity) {
                $seriesTimeline[4]['data'][] = [
                    'x' => $code,
                    'y' => [$start->timestamp * 1000, $end->timestamp * 1000],
                    'meta' => [
                        'title' => $project->title,
                        'status' => 'no_activities',
                        'percentage' => 100.0,
                        'counts' => ['total' => 0]
                    ]
                ];
                continue;
            }

            // Calculate percentages
            $pctCompleted = round(($completed / $totalActivities) * 100, 1);
            $pctUnder = round(($under / $totalActivities) * 100, 1);
            $pctNotStarted = round(($notStarted / $totalActivities) * 100, 1);
            $pctNotRequired = round(($notRequired / $totalActivities) * 100, 1);

            $sumPct = $pctCompleted + $pctUnder + $pctNotStarted + $pctNotRequired;
            if ($sumPct != 100) {
                $pctCompleted += (100 - $sumPct); 
            }

            $totalDurationMs = $end->timestamp * 1000 - $start->timestamp * 1000;

            $currentStartMs = $start->timestamp * 1000;

            $segments = [
                ['status' => 'completed', 'pct' => $pctCompleted, 'seriesIndex' => 0],
                ['status' => 'under_progress', 'pct' => $pctUnder, 'seriesIndex' => 1],
                ['status' => 'not_started', 'pct' => $pctNotStarted, 'seriesIndex' => 2],
                ['status' => 'no_required', 'pct' => $pctNotRequired, 'seriesIndex' => 3],
            ];

            foreach ($segments as $seg) {
                if ($seg['pct'] <= 0)
                    continue;

                $segmentDurationMs = ($seg['pct'] / 100) * $totalDurationMs;
                $segmentEndMs = $currentStartMs + $segmentDurationMs;

                $seriesTimeline[$seg['seriesIndex']]['data'][] = [
                    'x' => $code,
                    'y' => [$currentStartMs, $segmentEndMs],
                    'meta' => [
                        'title' => $project->title,
                        'status' => $seg['status'],
                        'percentage' => $seg['pct'],
                        'counts' => [
                            'total' => (int) $totalActivities,
                            'completed' => (int) $completed,
                            'under_progress' => (int) $under,
                            'not_started' => (int) $notStarted,
                            'no_required' => (int) $notRequired,
                        ]
                    ]
                ];

                $currentStartMs = $segmentEndMs;
            }
        }

        // Timeline range
        
        // if ($startDateFilter || $endDateFilter) {
        //     // $minYear = $startDateFilter
        //     //     ? Carbon::parse($startDateFilter)->subMonths(6)->startOfMonth()
        //     //     : ($minDate ? Carbon::parse($minDate)->subYear()->startOfYear() : now()->subYears(3));

        //     // $maxYear = $endDateFilter
        //     //     ? Carbon::parse($endDateFilter)->addMonths(6)->endOfMonth()
        //     //     : ($maxDate ? Carbon::parse($maxDate)->addYear()->endOfYear() : now()->addYears(3));

        //     $minYear = Carbon::parse($startDateFilter)->subMonths(1)->startOfDay();
        //     $maxYear = Carbon::parse($endDateFilter)->addMonths(1)->endOfDay();
        // } else {
        //     $minYear = $minDate ? Carbon::parse($minDate)->subYear()->startOfYear() : now()->subYears(3);
        //     $maxYear = $maxDate ? Carbon::parse($maxDate)->addYear()->endOfYear() : now()->addYears(3);
        // }

        if ($startDateFilter && $endDateFilter) {
            $minYear = Carbon::parse($startDateFilter)->startOfDay();
            $maxYear = Carbon::parse($endDateFilter)->endOfDay();
        } else {
            $minYear = $minDate ? Carbon::parse($minDate)->subMonths(6) : now()->subYears(3);
            $maxYear = $maxDate ? Carbon::parse($maxDate)->addMonths(6) : now()->addYears(3);
        }

        if ($isPMLT) {
            $allProjects = $this->projects->getModel()
                ->whereNotNull('activated_at')
                ->where('show_pms_dashboard', true)
                ->orderBy('title')
                ->get();
        } else {
            $allProjects = $this->projects->getAssignedProjects($authUser)
                ->filter(fn($p) => $p->show_pms_dashboard)
                ->sortBy('title')
                ->values();
        }

        return view('Project::Project.pmsDashboard', compact(
            'seriesTimeline',
            'projectNames',
            'projects',
            'minYear',
            'maxYear',
            'allProjects',
            'projectIds',
            'startDateFilter',
            'endDateFilter'
        ));
    }
}
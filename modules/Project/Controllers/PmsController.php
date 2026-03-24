<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Enums\ActivityLevel;
use Carbon\Carbon;

class PmsController
{
    public function dashboard(Request $request)
    {
        $projectIds = $request->query('project_ids', []);
        $startDateFilter = $request->query('start_date');
        $endDateFilter = $request->query('end_date');

        $query = Project::query()
            ->whereNotNull('activated_at')
            ->withCount([
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
            ['name' => 'No Required', 'data' => []],
            ['name' => 'No Activities', 'data' => []],
        ];

        $projectNames = [];
        $minDate = null;
        $maxDate = null;

        foreach ($projects as $idx => $project) {

            $code = $project->short_name ?: 'P' . ($idx + 1);
            $projectNames[] = $code;

            if ($project->start_date) {
                $minDate = $minDate ? min($minDate, $project->start_date) : $project->start_date;
            }
            if ($project->completion_date) {
                $maxDate = $maxDate ? max($maxDate, $project->completion_date) : $project->completion_date;
            }

            $total = max(1, $project->total_activities);

            $completed = $project->completed_count;
            $under = $project->under_progress_count;
            $notStarted = $project->not_started_count;
            $noRequired = $project->no_required_count;

            $hasAnyActivity = ($completed + $under + $notStarted + $noRequired) > 0;

            $start = $project->start_date
                ? Carbon::parse($project->start_date)
                : now()->subYears(3);

            $end = $project->completion_date
                ? Carbon::parse($project->completion_date)
                : now()->addYears(3);

            $totalDuration = $end->diffInSeconds($start) ?: 1;

            if (!$hasAnyActivity) {
                $seriesTimeline[4]['data'][] = [
                    'x' => $code,
                    'y' => [
                        $start->timestamp * 1000,
                        $end->timestamp * 1000
                    ],
                    'meta' => [
                        'title' => $project->title,
                        'status' => 'no_activities',
                        'percentage' => 100.0
                    ]
                ];
                continue;
            }

            $completedPct = ($completed / $total) * 100;
            $underPct = ($under / $total) * 100;
            $notStartedPct = ($notStarted / $total) * 100;
            $noRequiredPct = ($noRequired / $total) * 100;

            $percentages = [
                'completed' => $completedPct,
                'under_progress' => $underPct,
                'not_started' => $notStartedPct,
                'no_required' => $noRequiredPct,
            ];

            $currentStart = $start->copy();
            $accumulatedSeconds = 0;
            $keys = ['completed', 'under_progress', 'not_started', 'no_required'];

            foreach ($keys as $i => $key) {
                $pct = $percentages[$key];
                if ($pct <= 0)
                    continue;

                if ($i === count($keys) - 1) {
                    $segmentEnd = $end->copy();
                } else {
                    $segmentSeconds = ($pct / 100) * $totalDuration;
                    $accumulatedSeconds += $segmentSeconds;
                    $segmentEnd = $start->copy()->addSeconds($accumulatedSeconds);
                }

                $seriesIndex = match ($key) {
                    'completed' => 0,
                    'under_progress' => 1,
                    'not_started' => 2,
                    'no_required' => 3,
                };

                $seriesTimeline[$seriesIndex]['data'][] = [
                    'x' => $code,
                    'y' => [
                        $currentStart->timestamp * 1000,
                        $segmentEnd->timestamp * 1000
                    ],
                    'meta' => [
                        'title' => $project->title,
                        'status' => $key,
                        'percentage' => round($pct, 1),
                        'counts' => [
                            'total' => (int) $total,
                            'completed' => (int) $completed,
                            'under_progress' => (int) $under,
                            'not_started' => (int) $notStarted,
                            'no_required' => (int) $noRequired,
                        ]
                    ]
                ];

                $currentStart = $segmentEnd;
            }
        }

        // Timeline range
        if ($startDateFilter || $endDateFilter) {
            // $minYear = $startDateFilter
            //     ? Carbon::parse($startDateFilter)->subMonths(6)->startOfMonth()
            //     : ($minDate ? Carbon::parse($minDate)->subYear()->startOfYear() : now()->subYears(3));

            // $maxYear = $endDateFilter
            //     ? Carbon::parse($endDateFilter)->addMonths(6)->endOfMonth()
            //     : ($maxDate ? Carbon::parse($maxDate)->addYear()->endOfYear() : now()->addYears(3));

            $minYear = Carbon::parse($startDateFilter)->subMonths(1)->startOfDay();
            $maxYear = Carbon::parse($endDateFilter)->addMonths(1)->endOfDay();
        } else {
            $minYear = $minDate ? Carbon::parse($minDate)->subYear()->startOfYear() : now()->subYears(3);
            $maxYear = $maxDate ? Carbon::parse($maxDate)->addYear()->endOfYear() : now()->addYears(3);
        }

        $allProjects = Project::whereNotNull('activated_at')
            ->orderBy('title')
            ->get(['id', 'title', 'short_name']);

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
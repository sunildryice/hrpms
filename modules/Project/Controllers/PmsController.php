<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use Modules\Project\Models\Enums\ActivityStatus;
use Carbon\Carbon;

class PmsController
{
    public function dashboard(Request $request)
    {
        $projectIds = $request->query('project_ids', []); 

        $query = Project::query()
            ->whereNotNull('activated_at')
            ->withCount([
                'activities as completed_count' => fn($q) => $q->where('status', ActivityStatus::Completed),
                'activities as under_progress_count' => fn($q) => $q->where('status', ActivityStatus::UnderProgress),
                'activities as not_started_count' => fn($q) => $q->where('status', ActivityStatus::NotStarted),
                'activities as no_required_count' => fn($q) => $q->where('status', ActivityStatus::NoRequired),
                'activities as total_activities',
            ]);

        if (!empty($projectIds)) {
            $query->whereIn('id', $projectIds);
        }

        $projects = $query->orderBy('title')->get();

        // Chart 1: Percentage 
        $seriesPercent = [
            ['name' => 'Completed', 'data' => []],
            ['name' => 'Under Progress', 'data' => []],
            ['name' => 'Not Started', 'data' => []],
            ['name' => 'No Longer Required', 'data' => []],
        ];

        //  Chart 2: Timeline
        $seriesTimeline = [
            ['name' => 'Project Duration', 'data' => []],
        ];

        $projectNames = [];
        $minDate = null;
        $maxDate = null;
        $statusColors = [
            ActivityStatus::Completed->value => '#27ae60',
            ActivityStatus::UnderProgress->value => '#f8c90c',
            ActivityStatus::NotStarted->value => '#eb7d1d',
            ActivityStatus::NoRequired->value => '#e74c3c',
        ];

        foreach ($projects as $idx => $project) {
            $code = $project->short_name ?: 'P' . ($idx + 1);
            $projectNames[] = $code;

            if ($project->start_date) {
                $minDate = $minDate ? min($minDate, $project->start_date) : $project->start_date;
            }
            if ($project->completion_date) {
                $maxDate = $maxDate ? max($maxDate, $project->completion_date) : $project->completion_date;
            }

            $total = $project->total_activities ?: 1;

            $seriesPercent[0]['data'][] = round($project->completed_count / $total * 100, 1);
            $seriesPercent[1]['data'][] = round($project->under_progress_count / $total * 100, 1);
            $seriesPercent[2]['data'][] = round($project->not_started_count / $total * 100, 1);
            $seriesPercent[3]['data'][] = round($project->no_required_count / $total * 100, 1);

            $percentages = [
                'completed' => end($seriesPercent[0]['data']),
                'under_progress' => end($seriesPercent[1]['data']),
                'not_started' => end($seriesPercent[2]['data']),
                'no_required' => end($seriesPercent[3]['data']),
            ];

            $dominantKey = array_keys($percentages, max($percentages))[0] ?? 'not_started';

            $statusMap = [
                'completed' => ActivityStatus::Completed->value,
                'under_progress' => ActivityStatus::UnderProgress->value,
                'not_started' => ActivityStatus::NotStarted->value,
                'no_required' => ActivityStatus::NoRequired->value,
            ];

            $dominantValue = $statusMap[$dominantKey] ?? ActivityStatus::NotStarted->value;
            $color = $statusColors[$dominantValue] ?? '#6c757d';

            $startMs = $project->start_date?->timestamp * 1000 ?? now()->subYears(3)->timestamp * 1000;
            $endMs = $project->completion_date?->timestamp * 1000 ?? now()->addYears(3)->timestamp * 1000;

            $seriesTimeline[0]['data'][] = [
                'x' => $code,
                'y' => [$startMs, $endMs],
                // 'fillColor' => $color,
                'meta' => [
                    'title' => $project->title,
                    'percentages' => $percentages,
                ]
            ];
        }

        $minYear = $minDate ? Carbon::parse($minDate)->subYear()->startOfYear() : Carbon::now()->subYears(3);
        $maxYear = $maxDate ? Carbon::parse($maxDate)->addYear()->endOfYear() : Carbon::now()->addYears(3);

        $allProjects = Project::whereNotNull('activated_at')
            ->orderBy('title')
            ->get(['id', 'title', 'short_name']);

        return view('Project::Project.pmsDashboard', compact(
            'seriesPercent',
            'seriesTimeline',
            'projectNames',
            'projects',
            'minYear',
            'maxYear',
            'allProjects',
            'projectIds'
        ));
    }
}
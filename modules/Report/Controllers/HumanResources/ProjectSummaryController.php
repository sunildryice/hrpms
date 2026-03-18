<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Project;
use Modules\Report\Exports\HumanResources\ProjectSummaryExport;

class ProjectSummaryController extends Controller
{
    public function __construct(
        protected FiscalYearRepository $fiscalYears,
    ) {
    }

    public function index(Request $request)
    {

        $query = Project::query()
            ->whereNotNull('activated_at')
            ->withCount([
                'activities as completed_count' => fn($q) => $q->where('status', ActivityStatus::Completed),
                'activities as under_progress_count' => fn($q) => $q->where('status', ActivityStatus::UnderProgress),
                'activities as not_started_count' => fn($q) => $q->where('status', ActivityStatus::NotStarted),
                'activities as no_required_count' => fn($q) => $q->where('status', ActivityStatus::NoRequired),
                'activities as total_activities',
            ]);

        if ($request->filled('projects')) {
            $projectIds = is_array($request->projects) ? $request->projects : explode(',', $request->projects);
            $query->whereIn('id', $projectIds);
        }

        $projects = $query->orderBy('title')->paginate(50);

        $allProjects = Project::whereNotNull('activated_at')
            ->orderBy('title')
            ->get(['id', 'title', 'short_name']);

        return view('Report::HumanResources.ProjectSummary.index', [
            'projects' => $projects,
            'allProjects' => $allProjects,
            'requestData' => $request->all(),
        ]);
    }

    public function export(Request $request)
    {
        $projectIds = $request->filled('projects')
            ? (array) $request->projects
            : null;

        return Excel::download(new ProjectSummaryExport($projectIds), 'project_summary_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
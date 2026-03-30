<?php
namespace Modules\Project\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Project\Exports\ActivityExport;
use Modules\Project\Exports\ProjectActivityExport;
use Modules\Project\Exports\ProjectActivityDataExport;
use Modules\Project\Models\Project;

class ProjectActivityExportController extends Controller
{
    public function export(Request $request, Project $project)
    {
        $fileName = 'project_activities_' . $project->id . '.xlsx';
        return Excel::download(new ActivityExport($project), $fileName);
    }
    public function exportActivity(Request $request, Project $project)
    {
        $fileName = 'activity_plan_' . $project?->short_name . '.xlsx';
        return Excel::download(new ProjectActivityExport($project), $fileName);
    }
    public function exportData(Request $request, Project $project)
    {
        $fileName = 'activity_plan_' . $project->short_name . '_activities.xlsx';
        return Excel::download(new ProjectActivityDataExport($project), $fileName);
    }
}


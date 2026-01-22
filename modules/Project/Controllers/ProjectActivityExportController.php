<?php
namespace Modules\Project\Controllers;
use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Project\Exports\ActivityExport;
class ProjectActivityExportController extends Controller
{
    public function export(Request $request, Project $project)
    {
        $fileName = 'project_activities_' . $project->id . '.xlsx';
        return Excel::download(new ActivityExport($project), $fileName);
    }
}
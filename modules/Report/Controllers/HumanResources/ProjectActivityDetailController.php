<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ProjectActivityDetail;
use Modules\Report\Exports\HumanResources\ProjectActivityDetailExport;

class ProjectActivityDetailController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['project_id', 'activity_id', 'from_date', 'to_date']);

        $details = ProjectActivityDetail::query()
            ->with([
                'projectActivity:id,project_id,activity_stage_id,activity_level,parent_id,title,status,start_date,completion_date',
                'projectActivity.project:id,title,short_name',
                'projectActivity.stage:id,title',
                'projectActivity.parent:id,title',
            ])
            ->filterByRequest($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        $projects = Project::whereNotNull('activated_at')->orderBy('title')->get(['id', 'title', 'short_name']);
        $activities = ProjectActivity::orderBy('title')->get(['id', 'title', 'project_id']);

        return view('Report::HumanResources.ProjectActivityDetail.index', compact(
            'details', 'projects', 'activities'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'project_id', 'activity_id', 'from_date', 'to_date'
        ]);

        return Excel::download(
            new ProjectActivityDetailExport($filters),
            'project_activity_detail_report.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}

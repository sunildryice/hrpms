<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Report\Exports\HumanResources\AssignedActivityExport;

class AssignedActivityController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $activities,
    ) {
    }

    public function index(Request $request)
    {
        $employees = $this->employees->activeEmployees();
        $projects = $this->projects->getActiveProjects(null);

        if (!$request->filled('employee')) {
            return view('Report::HumanResources.AssignedActivity.index', [
                'employees' => $employees,
                'projects' => $projects,
                'activities' => collect(),
                'requestData' => $request->all(),
            ]);
        }

        $query = $this->activities->query()
            ->select([
                'project_activities.*',
                'projects.title as project_title',
                'parent.title as parent_title',
                'lkup_activity_stages.title as stage_title',
            ])
            ->join('projects', 'project_activities.project_id', '=', 'projects.id')
            ->leftJoin('project_activities as parent', 'project_activities.parent_id', '=', 'parent.id')
            ->leftJoin('lkup_activity_stages', 'project_activities.activity_stage_id', '=', 'lkup_activity_stages.id')
            ->whereIn('project_activities.activity_level', ['activity', 'sub_activity'])
            ->whereHas('members')
            ->with('members:id,full_name');

        // Filters
        if ($request->employee) {
            $query->whereHas('members', fn($q) => $q->where('user_id', $request->employee));
        }

        if ($request->project) {
            $query->where('project_activities.project_id', $request->project);
        }

        $activities = $query
            ->orderBy('projects.title')
            ->orderBy('project_activities.parent_id')
            ->orderBy('project_activities.activity_level')
            ->orderBy('project_activities.title')
            ->paginate(50);

        return view('Report::HumanResources.AssignedActivity.index', [
            'employees' => $employees,
            'projects' => $projects,
            'activities' => $activities,
            'requestData' => $request->all(),
        ]);
    }

    public function export(Request $request)
    {
        $employee = $request->filled('employee') ? (int) $request->employee : null;
        $project = $request->filled('project') ? (int) $request->project : null;

        return Excel::download(
            new AssignedActivityExport($employee, $project),
            'assigned-activities-report.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
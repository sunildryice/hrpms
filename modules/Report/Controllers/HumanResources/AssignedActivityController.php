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
        $projects = $this->projects->getActiveProjects();

        if (!$request->filled('employee')) {
            return view('Report::HumanResources.AssignedActivity.index', [
                'employees' => $employees,
                'projects' => $projects,
                'activities' => collect(),
                'requestData' => $request->all(),
            ]);
        }

        $query = $this->activities->getActivitiesDetail();

        // Filters
        if ($request->employee) {
            $query->whereHas('members', fn($q) => $q->where('user_id', $request->employee));
        }

        if ($request->project) {
            $query->where('project_activities.project_id', $request->project);
        }

        $activities = $query->paginate(50);

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
            new AssignedActivityExport($employee, $project), 'assigned-activities-report.xlsx', \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
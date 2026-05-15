<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\ApproachRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectThemeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Project;
use Modules\Report\Exports\HumanResources\ProjectSummaryExport;

class ProjectSummaryController extends Controller
{
    public function __construct(
        protected FiscalYearRepository $fiscalYears,
        protected UserRepository $userRepository,
        protected EmployeeRepository $employeeRepository,
        protected ProjectThemeRepository $projectThemeRepository,
        protected ApproachRepository $approachRepository,
        protected DistrictRepository $districtRepository,
    ) {
    }

    public function index(Request $request)
    {

        $query = Project::query()
            // ->whereNotNull('activated_at')
            ->with([
                'teamLead:id,full_name',
                'focalPerson:id,full_name',
            ])
            ->withCount([
                'activities as completed_count' => fn($q) => $q->where('status', ActivityStatus::Completed),
                'activities as under_progress_count' => fn($q) => $q->where('status', ActivityStatus::UnderProgress),
                'activities as not_started_count' => fn($q) => $q->where('status', ActivityStatus::NotStarted),
                'activities as no_required_count' => fn($q) => $q->where('status', ActivityStatus::NoRequired),
                'activities as total_activities',
            ]);


        if ($request->filled('team_lead_id')) {
            $query->where('team_lead_id', $request->team_lead_id);
        }

        if ($request->filled('focal_person_id')) {
            $query->where('focal_person_id', $request->focal_person_id);
        }

        if ($request->filled('project_theme_id')) {
            $query->where('project_theme_id', $request->project_theme_id);
        }

        if ($request->filled('approach_id')) {
            $approachId = (int) $request->approach_id;
            $query->where(function ($q) use ($approachId) {
                $q->whereJsonContains('approach_ids', $approachId)
                    ->orWhere('approach_ids', 'LIKE', '%"' . $approachId . '"%');
            });
        }

        if ($request->filled('district_id')) {
            $districtId = (int) $request->district_id;
            $query->where(function ($q) use ($districtId) {
                $q->whereJsonContains('approach_ids', $districtId)
                    ->orWhere('district_ids', 'LIKE', '%"' . $districtId . '"%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('activated_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNull('activated_at');
            }
        }

        // if ($request->filled('projects')) {
        //     $projectIds = is_array($request->projects) ? $request->projects : explode(',', $request->projects);
        //     $query->whereIn('id', $projectIds);
        // }

        $projects = $query->orderBy('title')->paginate(50);

        $teamLeads = $this->userRepository->getActiveUsers();
        $focalPersons = $this->userRepository->getActiveUsers();
        $projectThemes = $this->projectThemeRepository->getActive();
        $approaches = $this->approachRepository->getActive();
        $districts = $this->districtRepository->getDistricts();

        $allProjects = Project::whereNotNull('activated_at')
            ->orderBy('title')
            ->get(['id', 'title', 'short_name']);

        return view('Report::HumanResources.ProjectSummary.index', [
            'projects' => $projects,
            'allProjects' => $allProjects,
            'teamLeads' => $teamLeads,
            'focalPersons' => $focalPersons,
            'projectThemes' => $projectThemes,
            'approaches' => $approaches,
            'districts' => $districts,
            'requestData' => $request->all(),
        ]);
    }

    public function export(Request $request)
    {
        $filters = [
            'team_lead_id' => $request->team_lead_id,
            'focal_person_id' => $request->focal_person_id,
            'project_theme_id' => $request->project_theme_id,
            'approach_id' => $request->approach_id,
            'district_id' => $request->district_id,
            'status' => $request->status,
        ];

        return Excel::download(
            new ProjectSummaryExport($filters),
            'project_summary_report.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
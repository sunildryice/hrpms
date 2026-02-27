<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\TimeSheet\StoreRequest;
use Modules\Project\Requests\TimeSheet\UpdateRequest;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Repositories\ActivityTimeSheetRepository;

class TimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $timeSheets,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
    ) {
        $this->destinationPath = 'TimeSheet';
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->timeSheets->getQuery()
                ->with(['project', 'activity'])
                ->where('created_by', $authUser->id)
                ->orderBy('timesheet_date', 'asc')
                ->orderBy('project_id', 'asc')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project_id', function ($row) {
                    return $row->project?->short_name ?: $row->project?->title ?? '';
                })
                ->addColumn('activity_id', function ($row) {
                    return $row->activity?->title;
                })
                ->addColumn('timesheet_date', function ($row) {
                    // Return raw date for grouping
                    return $row->timesheet_date?->format('Y-m-d');
                })
                ->addColumn('timesheet_date_display', function ($row) {
                    // Display formatted date
                    return $row->timesheet_date?->format('M d, Y');
                })
                ->addColumn('attachment', function ($row) {
                    $attachment = '';
                    if (file_exists('storage/' . $row->attachment) && $row->attachment != '') {
                        $attachment = '<a href = "' . asset('storage/' . $row->attachment) . '" target = "_blank" class="fs-5" ';
                        $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                    }
                    return $attachment;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm open-timesheet-modal-form" href="';
                    $btn .= route('timesheet.show', $row->id) . '" rel="tooltip" title="View TimeSheet">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-timesheet-modal-form" href="';
                    $btn .= route('timesheet.edit', $row->id) . '" rel="tooltip" title="Edit TimeSheet">';
                    $btn .= '<i class="bi bi-pencil-square"></i></a>';

                    $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"';
                    $btn .= ' data-href="' . route('timesheet.destroy', $row->id) . '" rel="tooltip"';
                    $btn .= ' title="Delete TimeSheet"><i class="bi bi-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'attachment'])
                ->make(true);
        }

        return view('Project::TimeSheet.index');
    }


    public function create()
    {
        $authUser = auth()->user();
        $projects = $this->projects->getAssignedProjects($authUser);
        $activities = $this->projectActivities->getActivitiesByProject($authUser);
        return view('Project::TimeSheet.create', compact('projects', 'activities'));
    }



    public function store(StoreRequest $request, ProjectActivity $projectActivity)
    {
        $validated = $request->validated();
        $userId = auth()->id();
        $date = $validated['timesheet_date'];
        $entries = $request->input('entries', []);

        foreach ($entries as $idx => $entry) {
            $data = [
                'timesheet_date' => $date,
                'project_id' => $entry['project_id'],
                'activity_id' => $entry['activity_id'],
                'description' => $entry['description'] ?? null,
                'hours_spent' => $entry['hours_spent'],
                'created_by' => $userId,
            ];

            // handle per-entry attachment if present
            if ($request->hasFile("entries.{$idx}.attachment")) {
                $file = $request->file("entries.{$idx}.attachment");
                $filename = $file
                    ->storeAs($this->destinationPath . '/' . ($entry['project_id'] ?? '0'), time() . '_ts_' . $idx . '.' . $file->getClientOriginalExtension());
                $data['attachment'] = $filename;
            }

            $this->timeSheets->create($data);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Timesheet created successfully.',
            ]);
        }

        return redirect()
            ->route('timesheet.index')
            ->with('success_message', 'Timesheet created successfully.');
    }

    public function edit(ActivityTimeSheet $timesheet)
    {
        $authUser = auth()->user();
        $projects = $this->projects->getAssignedProjects($authUser);
        $activities = $this->projectActivities->getActivitiesByProject($authUser);
        return view('Project::TimeSheet.edit', compact('projects', 'timesheet', 'activities'));
    }

    public function show(ActivityTimeSheet $timesheet)
    {
        $authUser = auth()->user();
        $projects = $this->projects->getAssignedProjects($authUser);
        $activities = $this->projectActivities->getActivitiesByProject($authUser);
        return view('Project::TimeSheet.show', compact('projects', 'timesheet', 'activities'));
    }

    public function update(UpdateRequest $request, ActivityTimeSheet $timesheet)
    {
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $timesheet->activity_id, time() . '_timesheet.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();

        $this->timeSheets->update($timesheet->id, $inputs);

        return response()->json([
            'message' => 'Timesheet updated successfully.',
        ]);
    }

    public function destroy(ActivityTimeSheet $timesheet)
    {
        $this->timeSheets->destroy($timesheet->id);

        return response()->json([
            'message' => 'Timesheet deleted successfully.',
        ]);
    }
}

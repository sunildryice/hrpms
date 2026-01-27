<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
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
            $data = $this->timeSheets->query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('timesheet_date', function ($row) {
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
        $projects = $this->projects->query()->get();
        $activities = $this->projectActivities->query()->get();
        return view('Project::TimeSheet.create', compact('projects', 'activities'));
    }

    public function store(StoreRequest $request, ProjectActivity $projectActivity)
    {
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $projectActivity->id, time() . '_timesheet.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['created_by'] = auth()->id();

        $this->timeSheets->create($inputs);

        return response()->json([
            'message' => 'Timesheet created successfully.',
        ]);
    }

    public function edit(ActivityTimeSheet $timesheet)
    {
        $projects = $this->projects->query()->get();
        $activities = $this->projectActivities->query()->get();
        return view('Project::TimeSheet.edit', compact('projects', 'timesheet', 'activities'));
    }

    public function show(ActivityTimeSheet $timesheet)
    {
        $projects = $this->projects->query()->get();
        $activities = $this->projectActivities->query()->get();
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
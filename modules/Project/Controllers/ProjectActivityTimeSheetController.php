<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\ActivityTimeSheet; // ← add this
use Modules\Project\Repositories\ActivityTimeSheetRepository;
use Modules\Project\Requests\ActivityTimeSheet\StoreRequest;
use Modules\Project\Requests\ActivityTimeSheet\UpdateRequest;

class ProjectActivityTimeSheetController extends Controller
{
    public function __construct(
        protected ActivityTimeSheetRepository $activityTimeSheets
    ) {
        $this->destinationPath = 'ProjectActivity';
    }

    public function index(Request $request, ProjectActivity $projectActivity)
    {
        $data = $this->activityTimeSheets
            ->where('activity_id', '=', $projectActivity->id);
        $authUser = auth()->user();
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('timesheet_date', function ($row) {
                return $row->timesheet_date?->format('M d, Y');
            })
            ->addColumn('activity_title', function ($row) {
                return $row->activity?->title;
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

                $btn = ' <a class="btn btn-outline-primary btn-sm open-timesheet-modal-form" href="';
                $btn .= route('project-activity.timesheet.edit', $row->id) . '" rel="tooltip" title="Edit Timesheet">';
                $btn .= '<i class="bi bi-pencil-square"></i></a>';


                $btn .= ' <button class="btn btn-outline-danger btn-sm delete-record"
                data-href="';
                $btn .= route('project-activity-timesheet.destroy', $row->id) . '"
                data-id="';
                $btn .= $row->id . '" rel="tooltip" title="Delete Timesheet"> ';
                $btn .= '<i class="bi bi-trash"></i></button>';

                return $btn;
            })
            ->rawColumns(['action', 'status', 'attachment'])
            ->make(true);
    }
    public function create(ProjectActivity $projectActivity)
    {
        return view('Project::ProjectActivityTimeSheet.create', compact('projectActivity'));
    }

    public function edit(ActivityTimeSheet $timesheet)
    {
        $projectActivity = $timesheet->activity;
        return view('Project::ProjectActivityTimeSheet.edit', compact('projectActivity', 'timesheet'));
    }


    public function store(StoreRequest $request, ProjectActivity $projectActivity)
    {
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $projectActivity->id, time() . '_timesheet.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['activity_id'] = $projectActivity->id;
        $inputs['project_id'] = $projectActivity->project_id;
        $inputs['created_by'] = auth()->id();

        $this->activityTimeSheets->create($inputs);

        return response()->json([
            'message' => 'Timesheet created successfully.',
            'redirect' => route('project-activity.show', $projectActivity->id),
        ]);
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

        $this->activityTimeSheets->update($timesheet->id, $inputs);

        return response()->json([
            'message' => 'Timesheet updated successfully.',
            'redirect' => route('project-activity.show', $timesheet->activity_id),
        ]);
    }

    public function destroy(ActivityTimeSheet $timesheet)
    {
        $this->activityTimeSheets->destroy($timesheet->id);

        return response()->json([
            'message' => 'Timesheet deleted successfully.',
        ]);
    }
}
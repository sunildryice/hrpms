<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Modules\Project\Requests\ActivityStage\StoreRequest;
use Modules\Project\Requests\ActivityStage\UpdateRequest;
use Modules\Project\Repositories\ActivityStageRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityStageController extends Controller
{
    public function __construct(protected ActivityStageRepository $activityStageRepository) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = $this->activityStageRepository->query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->createdBy ? $row->createdBy->full_name : 'N/A';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('M d, Y') : 'N/A';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    $btn .= route('activity-stage.show', $row->id) . '" rel="tooltip" title="View Activity Stage">';
                    $btn .= '<i class="bi bi-eye"></i></a>';

                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    $btn .= route('activity-stage.edit', $row->id) . '" rel="tooltip" title="Edit Activity Stage">';
                    $btn .= '<i class="bi bi-pencil-square"></i></a>';

                    $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"';
                    $btn .= ' data-href="' . route('activity-stages.destroy', $row->id) . '" rel="tooltip"';
                    $btn .= ' title="Delete Activity Stage"><i class="bi bi-trash"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }


        return view('Project::ActivityStage.index');
    }


    public function create()
    {
        return view('Project::ActivityStage.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $inputs['activated_at'] = null;
        if (!empty($inputs['activated'])) {
            $inputs['activated_at'] = date('Y-m-d H:i:s');
        }

        $inputs['created_by'] = auth()->id();
        if (empty($inputs['activated_at'])) {
            $inputs['activated_at'] = null;
        }
        $this->activityStageRepository->create($inputs);

        return redirect()
            ->route('activity-stages.index')
            ->withSuccessMessage('Activity Stage created successfully.');
    }

    public function edit($id)
    {
        $activityStage = $this->activityStageRepository->find($id);

        return view('Project::ActivityStage.edit', compact('activityStage'));
    }

    public function show($id)
    {
        $activityStage = $this->activityStageRepository->find($id);

        return view('Project::ActivityStage.show', compact('activityStage'));
    }


    public function update($id, UpdateRequest $request)
    {
        $inputs = $request->validated();

        if (!empty($inputs['activated'])) {
            $inputs['activated_at'] = date('Y-m-d H:i:s');
        } else {
            $inputs['activated_at'] = null;
        }

        $inputs['updated_by'] = auth()->id();

        $this->activityStageRepository->update($id, $inputs);

        return redirect()
            ->route('activity-stages.index')
            ->withSuccessMessage('Activity Stage updated successfully.');
    }


    public function destroy($id, Request $request)
    {
        $this->activityStageRepository->destroy($id);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Activity Stage deleted successfully.'
            ]);
        }

        return redirect()
            ->route('activity-stages.index')
            ->withSuccessMessage('Activity Stage deleted successfully.');
    }
}

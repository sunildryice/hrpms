<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Requests\ActivityAccessPeriod\StoreRequest;
use Carbon\Carbon;
use Modules\Project\Requests\ActivityAccessPeriod\UpdateRequest;
use Modules\Project\Repositories\ActivityAccessPeriodRepository;
use Yajra\DataTables\Facades\DataTables;

class ActivityAccessPeriodController extends Controller
{
    public function __construct(protected ActivityAccessPeriodRepository $activityAccessPeriodRepository) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->activityAccessPeriodRepository->query();
            return DataTables::of($data)
                ->editColumn('start_date', function ($row) {
                    return $row->start_date ? Carbon::parse($row->start_date)->format('M j, Y') : 'N/A';
                })
                ->editColumn('end_date', function ($row) {
                    return $row->end_date ? Carbon::parse($row->end_date)->format('M j, Y') : 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-activity-access-period-modal-form" href="'
                        . route('activity-access-periods.edit', $row->id) . '" rel="tooltip" title="Edit">'
                        . '<i class="bi bi-pencil-square"></i></a>';
                    $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"'
                        . ' data-href="' . route('activity-access-periods.destroy', $row->id) . '" rel="tooltip"'
                        . ' title="Delete"><i class="bi bi-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Project::ActivityAccessPeriod.index');
    }

    public function create()
    {
        return view('Project::ActivityAccessPeriod.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $this->activityAccessPeriodRepository->create($inputs);

        if ($request->ajax()) {
            return response()->json(['message' => 'Access period created successfully.']);
        }

        return redirect()
            ->route('activity-access-periods.index')
            ->withSuccessMessage('Access period created successfully.');
    }

    public function edit($id)
    {
        $accessPeriod = $this->activityAccessPeriodRepository->find($id);
        return view('Project::ActivityAccessPeriod.edit', compact('accessPeriod'));
    }

    public function show($id)
    {
        $accessPeriod = $this->activityAccessPeriodRepository->find($id);
        return view('Project::ActivityAccessPeriod.show', compact('accessPeriod'));
    }

    public function update($id, UpdateRequest $request)
    {
        $inputs = $request->validated();

        $this->activityAccessPeriodRepository->update($id, $inputs);

        if ($request->ajax()) {
            return response()->json(['message' => 'Access period updated successfully.']);
        }

        return redirect()
            ->route('activity-access-periods.index')
            ->withSuccessMessage('Access period updated successfully.');
    }

    public function destroy($id, Request $request)
    {
        $this->activityAccessPeriodRepository->destroy($id);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Access period deleted successfully.'
            ]);
        }

        return redirect()
            ->route('activity-access-periods.index')
            ->withSuccessMessage('Access period deleted successfully.');
    }
}

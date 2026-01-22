<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Requests\ActivityUpdatePeriod\StoreRequest;
use Carbon\Carbon;
use Modules\Project\Requests\ActivityUpdatePeriod\UpdateRequest;
use Modules\Project\Repositories\ActivityUpdatePeriodRepository;
use Yajra\DataTables\Facades\DataTables;

class ActivityUpdatePeriodController extends Controller
{
    public function __construct(protected ActivityUpdatePeriodRepository $activityUpdatePeriodRepository) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->activityUpdatePeriodRepository->query();
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
                    $btn .= ' <a class="btn btn-outline-primary btn-sm open-activity-update-period-modal-form" href="'
                        . route('activity-update-periods.edit', $row->id) . '" rel="tooltip" title="Edit">'
                        . '<i class="bi bi-pencil-square"></i></a>';
                    $btn .= ' <a class="btn btn-outline-danger btn-sm delete-record" href="javascript:void(0)"'
                        . ' data-href="' . route('activity-update-periods.destroy', $row->id) . '" rel="tooltip"'
                        . ' title="Delete"><i class="bi bi-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $ranges = $this->activityUpdatePeriodRepository->query()->get(['id', 'start_date', 'end_date']);
        $currentActiveRanges = $this->activityUpdatePeriodRepository->query()
            ->where('is_active', true)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString())
            ->get(['start_date', 'end_date']);
        return view('Project::ActivityUpdatePeriod.index', compact('ranges', 'currentActiveRanges'));
    }

    public function create()
    {
        return view('Project::ActivityUpdatePeriod.create');
    }

    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $this->activityUpdatePeriodRepository->create($inputs);

        if ($request->ajax()) {
            return response()->json(['message' => 'Update period created successfully.']);
        }

        return redirect()
            ->route('activity-update-periods.index')
            ->withSuccessMessage('Update period created successfully.');
    }

    public function edit($id)
    {
        $accessPeriod = $this->activityUpdatePeriodRepository->find($id);
        return view('Project::ActivityUpdatePeriod.edit', compact('accessPeriod'));
    }

    public function show($id)
    {
        $accessPeriod = $this->activityUpdatePeriodRepository->find($id);
        return view('Project::ActivityUpdatePeriod.show', compact('accessPeriod'));
    }

    public function update($id, UpdateRequest $request)
    {
        $inputs = $request->validated();

        $this->activityUpdatePeriodRepository->update($id, $inputs);

        if ($request->ajax()) {
            return response()->json(['message' => 'Update period updated successfully.']);
        }

        return redirect()
            ->route('activity-update-periods.index')
            ->withSuccessMessage('Update period updated successfully.');
    }

    public function destroy($id, Request $request)
    {
        $this->activityUpdatePeriodRepository->destroy($id);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Update period deleted successfully.'
            ]);
        }

        return redirect()
            ->route('activity-update-periods.index')
            ->withSuccessMessage('Update period deleted successfully.');
    }
}

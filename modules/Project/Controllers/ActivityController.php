<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Repositories\ProjectActivityRepository;

class ActivityController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    )
    {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $activities = $this->projectActivity->with(['project', 'parent', 'stage', 'members'])
                ->whereIn('activity_level', ['activity', 'sub_activity'])
                ->whereHas('project', function ($query) use ($authUser) {
                    $query->where('completion_date', '>=', now())
                        ->whereNotNull('activated_at');
                })
                ->whereHas('members', function ($query) use ($authUser) {
                    $query->where('user_id', $authUser->id);
                });

            return DataTables::of($activities)
                ->addIndexColumn()
                ->editColumn('project', function ($row) {
                    return $row->getProjectShortName();
                })
                ->addColumn('activity_stage', function ($row) {
                    return $row->stageName();
                })
                ->addColumn('parent', function ($row) {
                    return $row->parentName();
                })
                ->addColumn('activity_level', function ($row) {
                    return ucfirst(str_replace('_', ' ', $row->activity_level));
                })
                ->rawColumns(['activity_level'])
                ->make(true);
        }
        return view('Project::Activity.index');
    }
}

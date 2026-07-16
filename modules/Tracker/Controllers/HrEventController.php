<?php

namespace Modules\Tracker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use Modules\Tracker\Repositories\HrEventRepository;
use Modules\Tracker\Requests\HrEvent\StoreRequest;
use Modules\Tracker\Requests\HrEvent\UpdateRequest;
use Yajra\DataTables\DataTables;

class HrEventController extends Controller
{
    public function __construct(
        HrEventRepository $hrEvents,
    )
    {
        $this->hrEvents = $hrEvents;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $this->authorize('manage-hr-event');

        if ($request->ajax()) {
            $data = $this->hrEvents->with(['project'])->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project_title', function ($row) {
                    return $row->getProjectTitle();
                })
                ->addColumn('event_date', function ($row) {
                    return $row->getEventDate();
                })
                ->addColumn('total_shortlisted', function ($row) {
                    return $row->event_type === 'Recruitment' ? $row->getTotalShortlisted() : '-';
                })
                ->addColumn('total_recruited', function ($row) {
                    return $row->event_type === 'Recruitment' ? $row->getTotalRecruited() : '-';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('hr-event.show', $row->id) . '" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-hr-event')) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('hr-event.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('manage-hr-event')) {
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete" ';
                        $btn .= 'data-href="' . route('hr-event.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Tracker::HrEvent.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-hr-event');

        $projects = Project::whereNotNull('activated_at')->get();

        return view('Tracker::HrEvent.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-hr-event');

        $inputs = $request->validated();
        $inputs['created_by'] = auth()->user()->id;

        $recruitments = $inputs['recruitments'] ?? [];
        unset($inputs['recruitments']);

        $record = $this->hrEvents->create($inputs);

        if ($record) {
            if (!empty($recruitments)) {
                foreach ($recruitments as $r) {
                    $record->recruitments()->create($r);
                }
            }

            return redirect()->route('hr-event.index')->withSuccessMessage('HR Event created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('HR Event could not be created.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hrEvent = $this->hrEvents->with(['recruitments'])->find($id);
        return view('Tracker::HrEvent.show', compact('hrEvent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage-hr-event');

        $hrEvent  = $this->hrEvents->with(['recruitments'])->find($id);
        $projects = Project::whereNotNull('activated_at')->get();

        $hrEvent->recruitments->transform(function ($r) {
            $r->onboard_date_formatted = $r->onboard_date?->format('Y-m-d');
            return $r;
        });

        return view('Tracker::HrEvent.edit', compact('hrEvent', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->authorize('manage-hr-event');

        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->user()->id;

        $recruitments = $inputs['recruitments'] ?? [];
        unset($inputs['recruitments']);

        $record = $this->hrEvents->update($id, $inputs);

        if ($record) {
            $record = $this->hrEvents->find($id);

            $record->recruitments()->delete();
            if (!empty($recruitments)) {
                foreach ($recruitments as $r) {
                    $record->recruitments()->create($r);
                }
            }

            return redirect()->route('hr-event.index')->withSuccessMessage('HR Event updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('HR Event could not be updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('manage-hr-event');

        $record = $this->hrEvents->destroy($id);

        if ($record) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'HR Event deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'HR Event could not be deleted.'
            ], 422);
        }
    }
}

<?php

namespace Modules\Tracker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\Project\Models\Project;
use Modules\Employee\Models\Employee;
use Modules\Tracker\Models\Enums\Ethnicity;
use Modules\Tracker\Models\Enums\EventRole;
use Modules\Tracker\Models\EventRoaster;
use Modules\Tracker\Repositories\EventRepository;
use Modules\Tracker\Requests\Event\StoreRequest;
use Modules\Tracker\Requests\Event\UpdateRequest;
use Modules\Tracker\Exports\EventExport;
use Yajra\DataTables\DataTables;

class EventController extends Controller
{
    public function __construct(
        EventRepository $events,
    )
    {
        $this->events = $events;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $this->authorize('manage-event');

        if ($request->ajax()) {
            $data = $this->events->with(['project'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('filter_from_date')) {
                $data->where('from_date', '>=', $request->filter_from_date);
            }
            if ($request->filled('filter_to_date')) {
                $data->where('to_date', '<=', $request->filter_to_date);
            }

            $data = $data->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project_title', function ($row) {
                    return $row->project?->short_name ?? 'N/A';
                })
                ->addColumn('from_date', function ($row) {
                    return $row->getFromDate();
                })
                ->addColumn('to_date', function ($row) {
                    return $row->getToDate();
                })
                ->addColumn('total_participants', function ($row) {
                    return $row->getTotalParticipants();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('event.show', $row->id) . '" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-event')) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('event.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('manage-event')) {
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete" ';
                        $btn .= 'data-href="' . route('event.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Tracker::Event.index');
    }

    public function export()
    {
        $this->authorize('manage-event');

        return (new EventExport())->download();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-event');

        $projects   = Project::whereNotNull('activated_at')->get();
        $ethnicities = Ethnicity::cases();
        $eventRoles  = EventRole::cases();
        $employees   = Employee::whereNotNull('activated_at')->orderBy('full_name')->get();

        return view('Tracker::Event.create', compact('projects', 'ethnicities', 'eventRoles', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-event');

        $inputs = $request->validated();
        $inputs['roaster_details'] = $request->has('roaster_details') ? 1 : 0;
        $inputs['created_by'] = auth()->id();

        if ($request->file('attachment')) {
            $inputs['attachment'] = $request->file('attachment')
                ->storeAs('tracker/events', time().'_'.random_int(1000, 9999).'_attachment.'.$request->file('attachment')->getClientOriginalExtension());
        }

        $record = $this->events->create($inputs);

        if ($record) {
            if ($request->has('accompanying_members')) {
                $record->accompanyingMembers()->sync($request->input('accompanying_members'));
            }

            if ($record->roaster_details && $request->has('roasters')) {
                foreach ($request->input('roasters') as $roaster) {
                    $record->roasters()->create([
                        'organisation'      => $roaster['organisation'],
                        'organisation_name' => $roaster['organisation_name'] ?? null,
                        'position'          => $roaster['position'] ?? null,
                        'ethnicity'         => $roaster['ethnicity'] ?? null,
                        'gender'            => $roaster['gender'] ?? null,
                        'created_by'        => auth()->id(),
                    ]);
                }
            }

            return redirect()->route('event.index')->withSuccessMessage('Event created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Event could not be created.');
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
        $event = $this->events->find($id);
        $event->load(['project', 'roasters', 'accompanyingMembers']);
        return view('Tracker::Event.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage-event');

        $event      = $this->events->find($id);
        $event->load('accompanyingMembers');
        $projects   = Project::whereNotNull('activated_at')->get();
        $ethnicities = Ethnicity::cases();
        $eventRoles  = EventRole::cases();
        $employees   = Employee::whereNotNull('activated_at')->orderBy('full_name')->get();

        return view('Tracker::Event.edit', compact('event', 'projects', 'ethnicities', 'eventRoles', 'employees'));
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
        $this->authorize('manage-event');

        $inputs = $request->validated();
        $inputs['roaster_details'] = $request->has('roaster_details') ? 1 : 0;
        $inputs['updated_by'] = auth()->id();

        if ($request->file('attachment')) {
            $event = $this->events->find($id);
            if ($event->attachment && Storage::exists($event->attachment)) {
                Storage::delete($event->attachment);
            }

            $inputs['attachment'] = $request->file('attachment')
                ->storeAs('tracker/events', time().'_'.random_int(1000, 9999).'_attachment.'.$request->file('attachment')->getClientOriginalExtension());
        }

        $record = $this->events->update($id, $inputs);

        if ($record) {
            if ($request->has('accompanying_members')) {
                $record->accompanyingMembers()->sync($request->input('accompanying_members'));
            } else {
                $record->accompanyingMembers()->sync([]);
            }

            if ($request->has('deleted_roasters')) {
                EventRoaster::whereIn('id', $request->input('deleted_roasters'))->delete();
            }

            if ($record->roaster_details && $request->has('roasters')) {
                foreach ($request->input('roasters') as $roaster) {
                    if (isset($roaster['id'])) {
                        $record->roasters()->where('id', $roaster['id'])->update([
                            'organisation'      => $roaster['organisation'],
                            'organisation_name' => $roaster['organisation_name'] ?? null,
                            'position'          => $roaster['position'] ?? null,
                            'ethnicity'         => $roaster['ethnicity'] ?? null,
                            'gender'            => $roaster['gender'] ?? null,
                            'updated_by'        => auth()->id(),
                        ]);
                    } else {
                        $record->roasters()->create([
                            'organisation'      => $roaster['organisation'],
                            'organisation_name' => $roaster['organisation_name'] ?? null,
                            'position'          => $roaster['position'] ?? null,
                            'ethnicity'         => $roaster['ethnicity'] ?? null,
                            'gender'            => $roaster['gender'] ?? null,
                            'created_by'        => auth()->id(),
                        ]);
                    }
                }
            }

            return redirect()->route('event.index')->withSuccessMessage('Event updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Event could not be updated.');
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
        $this->authorize('manage-event');

        $record = $this->events->destroy($id);

        if ($record) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'Event deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'Event could not be deleted.'
            ], 422);
        }
    }

    /**
     * Store a new roaster for the event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRoaster(Request $request, $id)
    {
        $this->authorize('manage-event');

        $request->validate([
            'organisation'      => 'required|in:HERDi,Government,Other',
            'organisation_name' => 'nullable|string|max:255',
            'position'          => 'nullable|string|max:255',
            'ethnicity'         => ['nullable', Rule::in(array_map(fn($e) => $e->value, Ethnicity::cases()))],
            'gender'            => 'nullable|in:Male,Female,Other',
        ]);

        $event = $this->events->find($id);

        $roaster = $event->roasters()->create([
            'organisation'      => $request->organisation,
            'organisation_name' => $request->organisation_name,
            'position'          => $request->position,
            'ethnicity'         => $request->ethnicity,
            'gender'            => $request->gender,
            'created_by'        => auth()->id(),
        ]);

        if ($roaster) {
            return response()->json([
                'type'    => 'success',
                'message' => 'Roaster added successfully.',
                'roaster' => $roaster,
            ], 200);
        }

        return response()->json([
            'type'    => 'error',
            'message' => 'Roaster could not be added.',
        ], 422);
    }

    /**
     * Remove a roaster from the event.
     *
     * @param  int  $id
     * @param  int  $roasterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyRoaster($id, $roasterId)
    {
        $this->authorize('manage-event');

        $roaster = EventRoaster::where('event_id', $id)->findOrFail($roasterId);
        $roaster->delete();

        return response()->json([
            'type'    => 'success',
            'message' => 'Roaster deleted successfully.',
        ], 200);
    }
}

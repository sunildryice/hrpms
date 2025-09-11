<?php

namespace Modules\EmployeeExit\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\AdvanceRequest\Notifications\AdvanceRequestSubmitted;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use DataTables;


use Modules\EmployeeExit\Requests\ExitHandOverNoteActivity\StoreRequest;
use Modules\EmployeeExit\Requests\ExitHandOverNoteActivity\UpdateRequest;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteProjectRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteActivityRepository;

class ExitHandOverNoteActivityController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param ExitHandOverNoteActivityRepository $exitHandOverNoteActivities
     * @param UserRepository $users
     */
    public function __construct(
        protected ActivityCodeRepository             $activityCodes,
        protected DistrictRepository                 $districts,
        protected EmployeeRepository                 $employees,
        protected ExitHandOverNoteRepository         $exitHandOverNote,
        protected FiscalYearRepository               $fiscalYears,
        protected AdvanceRequestRepository           $advanceRequests,
        protected ExitHandOverNoteProjectRepository  $exitHandOverNoteProjects,
        protected ExitHandOverNoteActivityRepository $exitHandOverNoteActivities,
        protected ProjectCodeRepository              $projects,
        protected UserRepository                     $users
    )
    {
        $this->destinationPath = 'employeeExit';
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $noteId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $exitHandOverNote = $this->exitHandOverNote->find($noteId);
            $data = $this->exitHandOverNoteActivities->select([
                'id','activity', 'activity_code_id', 'handover_note_id', 'organization', 'phone', 'email', 'comments'])
                ->whereHandoverNoteId($noteId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            // if ($authUser->can('update', $exitHandOverNote)) {
            $datatable->addColumn('action', function ($row) use ($authUser, $exitHandOverNote) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-activity-modal-form" href="';
                $btn .= route('activity.exit.handover.note.edit', [$row->handover_note_id, $row->id]) . '" rel="tooltip" title="Edit Activity"><i class="bi-pencil-square"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('activity.exit.handover.note.destroy', [$row->handover_note_id, $row->id]) . '" rel="tooltip" title="Delete Activity">';
                $btn .= '<i class="bi-trash"></i></a>';
                return $btn;
            });
            // }
            return $datatable->addColumn('activity', function ($row) {
                return $row->getActivity();
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;

    }

    /**
     * Show the form for creating a new advance request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($noteId)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($noteId);
        return view('EmployeeExit::ExitHandOverNote.Activity.create')
            ->withActivityCodes($this->activityCodes->getActiveActivityCodes())
            ->withExitHandOverNote($exitHandOverNote);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $noteId)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $inputs['handover_note_id'] = $noteId;
        $inputs['requester_id'] = auth()->id();
        $inputs['created_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;


        $exitHandOverNoteActivity = $this->exitHandOverNoteActivities->create($inputs);

        if ($exitHandOverNoteActivity) {
            return response()->json(['status' => 'ok',
                'exitHandOverNoteActivity' => $exitHandOverNoteActivity,
                'message' => 'Exit Hand Over Note Activity is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note Activity can not be added.'], 422);
    }


    /**
     * Show the specified advance request.
     *
     * @param $advanceRequestId
     * @return mixed
     */
    public function show($advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);

        return view('AdvanceRequest::show')
            ->withAdvanceRequest($advanceRequest);
    }


    /**
     * Show the form for editing the specified advance request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($noteId, $id)
    {
        $authUser = auth()->user();
        $exitHandOverNoteActivity = $this->exitHandOverNoteActivities->find($id);

        return view('EmployeeExit::ExitHandOverNote.Activity.edit')
            ->withAuthUser(auth()->user())
            ->withActivityCodes($this->activityCodes->getActiveActivityCodes())
            ->withExitHandOverNoteActivity($exitHandOverNoteActivity);
    }

    /**
     * Update the specified advance request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $noteId, $id)
    {
        $exitHandOverNoteActivity = $this->exitHandOverNoteActivities->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitHandOverNoteActivity = $this->exitHandOverNoteActivities->update($id, $inputs);
        if ($exitHandOverNoteActivity) {
            return response()->json(['status' => 'ok',
                'exitHandOverNoteActivity' => $exitHandOverNoteActivity,
                'message' => 'Exit Hand Over Note Activity is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note Activity can not be updated.'], 422);

    }

    /**
     * Remove the specified advance request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($noteId, $id)
    {
        $exitHandOverNoteActivities = $this->exitHandOverNoteActivities->find($id);
        // $this->authorize('delete', $advanceRequest);
        $flag = $this->exitHandOverNoteActivities->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit HandOver Note Activity is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit HandOver Note Activity can not deleted.',
        ], 422);
    }

}

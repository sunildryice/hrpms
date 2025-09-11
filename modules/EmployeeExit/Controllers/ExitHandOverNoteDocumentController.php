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


use Modules\EmployeeExit\Requests\ExitHandOverNoteDocument\StoreRequest;
use Modules\EmployeeExit\Requests\ExitHandOverNoteDocument\UpdateRequest;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteProjectRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteActivityRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteDocumentRepository;

class ExitHandOverNoteDocumentController extends Controller
{

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
        ActivityCodeRepository             $activityCodes,
        DistrictRepository                 $districts,
        EmployeeRepository                 $employees,
        ExitHandOverNoteRepository         $exitHandOverNote,
        FiscalYearRepository               $fiscalYears,
        AdvanceRequestRepository           $advanceRequests,
        ExitHandOverNoteProjectRepository  $exitHandOverNoteProjects,
        ExitHandOverNoteActivityRepository $exitHandOverNoteActivities,
        ExitHandOverNoteDocumentRepository $exitHandOverNoteDocuments,
        ProjectCodeRepository              $projects,
        UserRepository                     $users
    )
    {
        $this->activityCodes = $activityCodes;
        $this->districts = $districts;
        $this->projects = $projects;
        $this->employees = $employees;
        $this->exitHandOverNote = $exitHandOverNote;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->exitHandOverNoteProjects = $exitHandOverNoteProjects;
        $this->exitHandOverNoteActivities = $exitHandOverNoteActivities;
        $this->exitHandOverNoteDocuments = $exitHandOverNoteDocuments;
        $this->users = $users;
        $this->destinationPath = 'exithandovernotedocument';
    }

    /**
     * Display a listing of the exit handover note
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $exitHandOverNoteId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $exitHandOverNote = $this->exitHandOverNote->find($exitHandOverNoteId);
            $data = $this->exitHandOverNoteDocuments->select([
                'id', 'handover_note_id', 'attachment_name', 'attachment_type', 'attachment'])
                ->whereHandoverNoteId($exitHandOverNoteId);
            return  DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('attachment', function ($row) {
                    $attachment = 'File does not exists.';
                    if (file_exists('storage/' . $row->attachment) && $row->attachment != '') {
                        $attachment = '<a href = "' . asset('storage/' . $row->attachment) . '" target = "_blank" class="fs-5" ' .
                            $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                    }
                    return $attachment;
                })->addColumn('action', function ($row) use ($authUser, $exitHandOverNote) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-document-modal-form" href="';
                    $btn .= route('document.exit.handover.note.edit', [$row->handover_note_id, $row->id]) . '" rel="tooltip" title="Edit Document"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('document.exit.handover.note.destroy', [$row->handover_note_id, $row->id]) . '" rel="tooltip" title="Delete Document">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'attachment'])
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
    public function create($exitHandOverNoteId)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($exitHandOverNoteId);
        return view('EmployeeExit::ExitHandOverNote.Document.create')
            ->withExitHandOverNote($exitHandOverNote);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $exitHandOverNoteId)
    {

        $authUser = auth()->user();
        // $this->authorize('create-advance-request');
        $inputs = $request->validated();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $inputs['handover_note_id'] = $exitHandOverNoteId;
        $inputs['requester_id'] = auth()->id();
        $inputs['created_by'] = auth()->id();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;


        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath, time() . '_attachement.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
            $inputs['attachment_type'] = $request->file('attachment')->getClientOriginalExtension();
        }


        $exitHandOverNoteDocument = $this->exitHandOverNoteDocuments->create($inputs);

        if ($exitHandOverNoteDocument) {
            return response()->json(['status' => 'ok',
                'exitHandOverNoteDocument' => $exitHandOverNoteDocument,
                'message' => 'Exit Hand Over Note Document is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note Document can not be added.'], 422);
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
     * Show the form for editing the specified Hand over Documentsssss.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($noteId, $id)
    {
        $authUser = auth()->user();
        $exitHandOverNoteDocument = $this->exitHandOverNoteDocuments->find($id);

        return view('EmployeeExit::ExitHandOverNote.Document.edit')
            ->withAuthUser(auth()->user())
            ->withExitHandOverNoteDocument($exitHandOverNoteDocument);
    }

    /**
     * Update the specified Hand Over Note in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $noteId, $id)
    {
        $exitHandOverNoteDocuments = $this->exitHandOverNoteDocuments->find($id);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath, time() . '_attachement.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
            $inputs['attachment_type'] = $request->file('attachment')->getClientOriginalExtension();
        }

        $exitHandOverNoteDocuments = $this->exitHandOverNoteDocuments->update($id, $inputs);
        if ($exitHandOverNoteDocuments) {
            return response()->json(['status' => 'ok',
                'exitHandOverNoteDocuments' => $exitHandOverNoteDocuments,
                'message' => 'Exit Hand Over Note Document is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note Document can not be updated.'], 422);

    }

    /**
     * Remove the specified Hand Over Document from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($noteId, $id)
    {
        $exitHandOverNoteDocuments = $this->exitHandOverNoteDocuments->find($id);
        $flag = $this->exitHandOverNoteDocuments->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit HandOver Note Document is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit HandOver Note Document can not deleted.',
        ], 422);
    }

}

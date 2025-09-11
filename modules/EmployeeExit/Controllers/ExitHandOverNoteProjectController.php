<?php

namespace Modules\EmployeeExit\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Requests\ExitHandOverNoteProject\UpdateRequest;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use DataTables;
use Modules\EmployeeExit\Requests\ExitHandOverNoteProject\StoreRequest;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteProjectRepository;

class ExitHandOverNoteProjectController extends Controller
{

    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param ExitHandOverNoteRepository $exitHandOverNote
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param ProjectCodeRepository $projects
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository                $employees,
        protected ExitHandOverNoteRepository        $exitHandOverNote,
        protected FiscalYearRepository              $fiscalYears,
        protected ExitHandOverNoteProjectRepository $exitHandOverNoteProjects,
        protected ProjectCodeRepository             $projects,
        protected UserRepository                    $users
    )
    {
        $this->destinationPath = 'exitHandOver';
    }

    /**
     * Display a listing of the handover notes
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $exitHandOverNoteId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $exitHandOverNote = $this->exitHandOverNote->find($exitHandOverNoteId);
            $data = $this->exitHandOverNoteProjects->select([
                'id','project', 'project_code_id', 'handover_note_id', 'project_status', 'action_needed', 'partners', 'budget', 'critical_issues'])
                ->whereHandoverNoteId($exitHandOverNoteId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            // if ($authUser->can('update', $exitHandOverNote)) {
            $datatable->addColumn('action', function ($row) use ($authUser, $exitHandOverNote) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-handover-project-modal-form" href="';
                $btn .= route('project.exit.handover.note.edit', [$row->handover_note_id, $row->id]) . '"><i class="bi-pencil-square" rel="tooltip" title="Edit Project"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('project.exit.handover.note.destroy', [$row->handover_note_id, $row->id]) . '" rel="tooltip" title="Delete Project">';
                $btn .= '<i class="bi-trash"></i></a>';
                return $btn;
            });
            // }
            return $datatable->addColumn('project', function ($row) {
                return $row->getProject();
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;

    }

    /**
     * Show the form for creating a handover notes.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($noteId)
    {
        $authUser = auth()->user();
        $exitHandOverNote = $this->exitHandOverNote->find($noteId);
        return view('EmployeeExit::ExitHandOverNote.Project.create')
            ->withProjectCodes($this->projects->getActiveProjectCodes())
            ->withExitHandOverNote($exitHandOverNote);
    }


    /**
     * Store a newly created exit hand over Note in storage.
     *
     * @param \Modules\EmployeeExit\Requests\StoreRequest $request
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


        $exitHandOverNoteProject = $this->exitHandOverNoteProjects->create($inputs);

        if ($exitHandOverNoteProject) {
            return response()->json(['status' => 'ok',
                'exitHandOverNoteProject' => $exitHandOverNoteProject,
                'message' => 'Exit Hand Over Note Project is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note Project can not be added.'], 422);
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
        $exitHandOverNoteProject = $this->exitHandOverNoteProjects->find($id);
        $projectCodes = $this->projects->getActiveProjectCodes();

        return view('EmployeeExit::ExitHandOverNote.Project.edit')
            ->withAuthUser(auth()->user())
            ->withProjectCodes($projectCodes)
            ->withExitHandOverNoteProject($exitHandOverNoteProject);
    }

    /**
     * Update the specified advance request in storage.
     *
     * @param UpdateRequest $request
     * @param $noteId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $noteId, $id)
    {
        $exitHandOverNoteProject = $this->exitHandOverNoteProjects->find($id);
        // $this->authorize('update', $exitHandOverNoteProject);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitHandOverNoteProject = $this->exitHandOverNoteProjects->update($id, $inputs);
        if ($exitHandOverNoteProject) {
            return response()->json(['status' => 'ok',
                'exitHandOverNoteProject' => $exitHandOverNoteProject,
                'message' => 'Exit Hand Over Note Project is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit Hand Over Note Project can not be updated.'], 422);

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
        $exitHandOverNoteProjects = $this->exitHandOverNoteProjects->find($id);
        // $this->authorize('delete', $advanceRequest);
        $flag = $this->exitHandOverNoteProjects->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit HandOver Note Project is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit HandOver Note Project can not deleted.',
        ], 422);
    }

}

<?php

namespace Modules\Master\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Requests\ProjectCode\StoreRequest;
use Modules\Master\Requests\ProjectCode\UpdateRequest;

class ProjectCodeController extends Controller
{
    /**
     * The project code repository instance.
     *
     * @var ProjectCodeRepository
     */
    protected $projectCodes;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ProjectCodeRepository $projectCodes
    ) {
        $this->projectCodes = $projectCodes;
    }

    /**
     * Display a listing of the project code.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->projectCodes->select([
                'id', 'title', 'short_name', 'description', 'activated_at', 'created_by', 'updated_at',
            ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-project-modal-form" href="';
                    $btn .= route('master.project.codes.edit', $row->id).'"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="'.route('master.project.codes.destroy', $row->id).'">';
                    $btn .= '<i class="bi-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Master::ProjectCode.index');
    }

    /**
     * Show the form for creating a new project code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ProjectCode.create');
    }

    /**
     * Store a newly created project code in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $projectCode = $this->projectCodes->create($inputs);
        if ($projectCode) {
            return response()->json(['status' => 'ok',
                'project code' => $projectCode,
                'message' => 'Project code is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Project code can not be added.'], 422);
    }

    /**
     * Display the specified project code.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $projectCode = $this->projectCodes->find($id);

        return response()->json(['status' => 'ok', 'projectCode' => $projectCode], 200);
    }

    /**
     * Show the form for editing the specified project code.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $projectCode = $this->projectCodes->find($id);

        return view('Master::ProjectCode.edit')
            ->with([
                'projectCode' => ($projectCode),
            ]);
    }

    /**
     * Update the specified project code in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $projectCode = $this->projectCodes->update($id, $inputs);
        if ($projectCode) {
            return response()->json(['status' => 'ok',
                'project code' => $projectCode,
                'message' => 'Project code is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Project code can not be updated.'], 422);
    }

    /**
     * Remove the specified project code from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->projectCodes->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Project code is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Project code can not deleted.',
        ], 422);
    }
}

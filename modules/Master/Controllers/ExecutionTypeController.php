<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Requests\ExecutionType\StoreRequest;
use Modules\Master\Requests\ExecutionType\UpdateRequest;

use DataTables;
use Modules\Master\Repositories\ExecutionRepository;

class ExecutionTypeController extends Controller
{
    public function __construct(
        protected ExecutionRepository $executions
    )
    {
    }

    /**
     * Display a listing of the dsa category.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->executions->select('*')->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    $btn .= route('master.execution.types.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    if(!$row->inventoryItems()->count()){
                        $btn .= 'data-href="' . route('master.execution.types.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::ExecutionType.index', ['executinos' => $this->executions->all()]);
    }

    /**
     * Show the form for creating a new dsa category.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ExecutionType.create');
    }

    /**
     * Store a newly created dsa category in storage.
     *
     * @param \Modules\Master\Requests\DsaCategory\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $execution = $this->executions->create($inputs);
        if ($execution) {
            return response()->json(['status' => 'ok',
                'execution' => $execution,
                'message' => 'Execution Type is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Execution Type can not be added.'], 422);
    }

    /**
     * Display the specified dsa category.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $execution = $this->executions->find($id);
        return response()->json(['status' => 'ok', 'execution' => $execution], 200);
    }

    public function edit($id)
    {
        $execution = $this->executions->find($id);
        return view('Master::ExecutionType.edit', ['execution' => $execution]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $execution = $this->executions->update($id, $inputs);
        if ($execution) {
            return response()->json(['status' => 'ok',
                'execution' => $execution,
                'message' => 'Execution Type is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Execution Type can not be updated.'], 422);
    }

    public function destroy($id)
    {
        $flag = $this->executions->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Execution Type is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Execution Type can not deleted.',
        ], 422);
    }
}

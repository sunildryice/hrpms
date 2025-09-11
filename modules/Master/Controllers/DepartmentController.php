<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Requests\Department\StoreRequest;
use Modules\Master\Requests\Department\UpdateRequest;

use DataTables;

class DepartmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DepartmentRepository $departments
     * @return void
     */
    public function __construct(
        DepartmentRepository $departments
    )
    {
        $this->departments = $departments;
    }

    /**
     * Display a listing of the department.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->departments->select([
                'id', 'title', 'created_by', 'updated_at'
            ])->orderBy('title');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-department-modal-form" href="';
                    $btn .= route('master.departments.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.departments.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::Department.index')
            ->withDepartments($this->departments->all());
    }

    /**
     * Show the form for creating a new department.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::Department.create');
    }

    /**
     * Store a newly created department in storage.
     *
     * @param \Modules\Master\Requests\Department\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $department = $this->departments->create($inputs);
        if ($department) {
            return response()->json(['status' => 'ok',
                'department' => $department,
                'message' => 'Department is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Department can not be added.'], 422);
    }

    /**
     * Display the specified department.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $department = $this->departments->find($id);
        return response()->json(['status' => 'ok', 'department' => $department], 200);
    }

    /**
     * Show the form for editing the specified department.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $department = $this->departments->find($id);
        return view('Master::Department.edit')
            ->withDepartment($department);
    }

    /**
     * Update the specified department in storage.
     *
     * @param \Modules\Master\Requests\Department\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $department = $this->departments->update($id, $inputs);
        if ($department) {
            return response()->json(['status' => 'ok',
                'department' => $department,
                'message' => 'Department is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Department can not be updated.'], 422);
    }

    /**
     * Remove the specified department from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->departments->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Department is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Department can not deleted.',
        ], 422);
    }
}

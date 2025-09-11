<?php

namespace Modules\Privilege\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Privilege\Repositories\PermissionRepository;
use Modules\Privilege\Requests\Permission\StoreRequest;
use Modules\Privilege\Requests\Permission\UpdateRequest;

use DataTables;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param PermissionRepository $permissions
     * @return void
     */
    public function __construct(PermissionRepository $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->permissions->select(['*'])
                ->with(['parent'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('privilege.permissions.view', $row->id) . '"><i class="bi-eye"></i></a>&emsp;';
                    $btn .= '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-modal-form" href="';
                    $btn .= route('privilege.permissions.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    return $btn;
                })->addColumn('active', function ($row) {
                    return $row->getActive();
                })->addColumn('parent', function ($row) {
                    return $row->getParent();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Privilege::Permission.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->permissions->select(['id', 'permission_name'])
            ->where('parent_id', '=', 0)
            ->orderby('permission_name', 'asc')->get();
        return view('Privilege::Permission.create')
            ->withPermissions($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['parent_id'] = $request->parent_id ?: 0;
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : null;
        $permission = $this->permissions->create($inputs);
        if ($permission) {
            return response()->json(['status' => 'ok',
                'permission' => $permission,
                'message' => 'Permission is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Permission can not be added.'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $permission = $this->permissions->find($id);
        return response()->json(['status' => 'ok', 'permission' => $permission], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permissions = $this->permissions->select(['id', 'permission_name'])
            ->where('parent_id', '=', 0)
            ->orderby('permission_name', 'asc')->get();
        return view('Privilege::Permission.edit')
            ->withPermission($this->permissions->find($id))
            ->withPermissions($permissions);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['parent_id'] = $request->parent_id ?: 0;
        $inputs['activated_at'] = $request->active ? date('Y-m-d H:i:s') : null;
        $permission = $this->permissions->update($id, $inputs);
        if ($permission) {
            return response()->json(['status' => 'ok',
                'permission' => $permission,
                'message' => 'Permission is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Permission can not be updated.'], 422);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $flag = $this->permissions->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Permission is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Permission can not deleted.',
        ], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return mixed
     */
    public function view($id)
    {
        $permission = $this->permissions->with(['roles', 'roles.users', 'roles.users.office'])->find($id);
        return view('Privilege::Permission.view')
            ->withPermission($permission);
    }
}

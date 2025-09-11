<?php

namespace Modules\Privilege\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;
use Modules\Privilege\Repositories\PermissionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Requests\Role\StoreRequest;
use Modules\Privilege\Requests\Role\UpdateRequest;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param RoleRepository $roles
     * @param PermissionRepository $permissions
     * @return void
     */
    public function __construct(
        RoleRepository       $roles,
        PermissionRepository $permissions
    )
    {
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->roles->select(['*'])
                ->where('id', '<>', 1);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('privilege.roles.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    return $btn;
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Privilege::Role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->permissions->with(['childrens' => function ($query) {
            $query->whereNotNull('activated_at');
        }])->where('parent_id', '=', 0)
            ->whereNotNull('activated_at')
            ->get();
        return view('Privilege::Role.create')
            ->withPermissions($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $role = $this->roles->create($inputs);
        if ($role) {
            return redirect()->route('privilege.roles.index')
                ->withSuccessMessage('Role successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Role can not be added.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->roles->find($id);
        return response()->json(['status' => 'ok', 'role' => $role], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->roles->find($id);
        $selectedPermissions = $role->permissions()->pluck('id')->toArray();
        $permissions = $this->permissions->with(['childrens' => function ($query) {
            $query->whereNotNull('activated_at');
        }])->where('parent_id', '=', 0)
            ->whereNotNull('activated_at')
            ->get();

        return view('Privilege::Role.edit')
            ->withRole($this->roles->find($id))
            ->withSelectedPermissions($selectedPermissions)
            ->withPermissions($permissions);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $role = $this->roles->update($id, $inputs);
        if ($role) {
            return redirect()->route('privilege.roles.index')
                ->withSuccessMessage('Role successfully updated.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Role can not be updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->roles->destroy($id);
    }

    public function view($id)
    {
        $role = $this->roles->with(['users', 'permissions'])->find($id);
        return view('Privilege::Role.view')
            ->withRole($role);
    }

}

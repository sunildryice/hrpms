<?php

namespace Modules\Privilege\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use DataTables;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @param RoleRepository $roles
     * @param UserRepository $users
     */
    public function __construct(
        RoleRepository $roles,
        UserRepository $users
    )
    {
        $this->roles = $roles;
        $this->users = $users;
    }

    /**
     * Display a listing of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->users->with(['roles', 'employee.office'])
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('id', 1);
                })->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('roles', function ($row) {
                    $roles = '';
                    foreach ($row->getRoles() as $role){
                        $roles .= $role .'<br />';
                    }
                    return $roles;
                })->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('employee_code', function ($row) {
                    return $row->getEmployeeCode();
                })->addColumn('user_id', function ($row) {
                    return $row->id;
                })->addColumn('office_name', function ($row) {
                    return $row->getOfficeName();
                })->addColumn('tenure_office_name', function ($row) {
                    return $row->employee->latestTenure->getOfficeName();
                })->rawColumns(['roles'])
                ->make(true);
        }
        return view('Privilege::User.index');
    }

    public function show($id)
    {
        $user = $this->users->find($id);
        return view('Privilege::User.show')
            ->withUser($user);
    }
}

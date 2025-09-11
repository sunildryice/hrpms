<?php

namespace Modules\Privilege\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Modules\Configuration\Repositories\DepartmentRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * The role repository instance.
     *
     * @var RoleRepository
     */
    protected $roles;

    /**
     * The office repository instance.
     *
     * @var OfficeRepository
     */
    protected $offices;

    /**
     * The department repository instance.
     *
     * @var DepartmentRepository
     */
    protected $departments;

    /**
     * The user repository instance.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        RoleRepository $roles,
        OfficeRepository $offices,
        DepartmentRepository $departments,
        UserRepository $users
    ) {
        $this->roles = $roles;
        $this->offices = $offices;
        $this->departments = $departments;
        $this->users = $users;
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->users->find($id);

        return response()->json([
            'type' => 'success',
            'user' => $user,
            'office' => $user->office,
            'accountCodes' => $user->accountCodes,
            'awardCodes' => $user->awardCodes,
            'budgetCodes' => $user->budgetCodes,
            'monitoringCodes' => $user->monitoringCodes,
        ], 200);
    }

    /**
     * check email available or not
     *
     * @return \Illuminate\Http\Response
     */
    public function validateEmail(Request $request)
    {
        $available = $this->users->where('email_address', '=', $request->email_address)->first() ? false : true;

        return response()->json(['valid' => $available], 200);
    }

    /**
     * check email exist or not
     *
     * @return \Illuminate\Http\Response
     */
    public function checkExist(Request $request)
    {
        $exist = $this->users->where('email_address', '=', $request->email_address)->first() ? true : false;

        return response()->json(['valid' => $exist], 200);
    }

    /**
     * search user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $this->users->select(['*'])->where('is_active', '1');
        if ($request->get('office_id')) {
            $query->where('office_id', $request->office_id);
        }
        if ($request->get('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        $users = $query->orderby('full_name')->get();

        return response()->json([
            'users' => $users,
        ], 200);
    }

    public function officeUsers($officeId)
    {
        return response()->json([
            'users' => $this->users->query()
                ->select(['id', 'full_name', 'employee_id'])
                ->whereHas('employee', function ($q) use ($officeId) {
                    $q->where('office_id', $officeId);
                })
                ->get(),
        ], 200);
    }
}

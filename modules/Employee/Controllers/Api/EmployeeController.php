<?php

namespace Modules\Employee\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Privilege\Repositories\UserRepository;

class EmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected UserRepository $user
    ) {}

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function officeEmployees($officeId)
    {
        return response()->json([
            'employees' => $this->employees->query()->where('office_id', $officeId)->get(),
        ], 200);
    }
}

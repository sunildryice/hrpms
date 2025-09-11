<?php

namespace Modules\ProbationaryReview\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\ProbationaryIndicatorRepository;
use Modules\Master\Repositories\ProbationaryReviewTypeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestCreated;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestForApprove;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewIndicatorRepository;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewRepository;
use Modules\ProbationaryReview\Requests\SendToRequest;
use Modules\ProbationaryReview\Requests\StoreRequest;
use Modules\ProbationaryReview\Requests\UpdateRequest;

class ProbationaryReviewRequestController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected ProbationaryIndicatorRepository $probationaryIndicator,
        protected ProbationaryReviewRepository $probationaryReview,
        protected ProbationaryReviewIndicatorRepository $probationaryReviewIndicator,
        protected ProbationaryReviewTypeRepository $probationaryReviewType,
        protected RoleRepository $roles,
        protected UserRepository $user
    ) {
        $this->destinationPath = 'probationaryReview';

    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $authUser = auth()->user();
        $employee_id = auth()->user()->employee_id;
        if ($request->ajax()) {
            $data = $this->probationaryReview->with(['reviewer'])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('review_type', function ($row) {
                    return $row->getReviewType();
                })->addColumn('review_date', function ($row) {
                    return $row->getReviewDate();
                })->addColumn('reviewer', function ($row) {
                    return $row->getReviewerName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('view', $row)) {
                        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('probation.review.request.view', $row->id).'" rel="tooltip" title="Details"><i class="bi-eye"></i></a> ';
                    }
                    if ($authUser->can('sendTo', $row)) {
                        $btn = '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('send.probation.review.request.sendTo', $row->id).'" rel="tooltip" title="Send To"><i class="bi-upload"></i></a> ';
                    }

                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm open-probation-modal-form" href="';
                        $btn .= route('probation.review.requests.edit', $row->id).'" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('probation.review.requests.destroy', $row->id).'" rel="tooltip" title="Delete">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('ProbationaryReview::index');
    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function list(Request $request)
    {
        $userId = auth()->id();
        $authUser = auth()->user();
        $employee_id = auth()->user()->employee_id;

        if ($request->ajax()) {
            $data = $this->probationaryReview
                ->select(['*'])
                ->with('employee')
                ->whereBetween('next_probation_complete_date', [date('Y-m-d'), date('Y-m-d', strtotime('+14 days'))])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addIndexColumn()->addColumn('department_name', function ($row) {
                    return $row->employee->getDepartmentName();
                })->addIndexColumn()->addColumn('designation_name', function ($row) {
                    return $row->employee->getDesignationName();
                })->addIndexColumn()->addColumn('probation_end_date', function ($row) {
                    return $row->getProbationEndDate();
                })
                ->make(true);
        }

        return view('ProbationaryReview::list');
    }

    /**
     * Show the form for creating a new probation review.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $activeStaffs = $this->employees->select(['id', 'full_name', 'employee_code'])
            ->whereNull('probation_complete_date')
            ->whereNotNull('activated_at')
            ->orderBy('full_name', 'asc')->get();
        $employees = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });

        return view('ProbationaryReview::create')
            ->with([
                'employees' => ($employees),
                'probationaryReviewTypes' => ($this->probationaryReviewType->get()),
            ]);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['created_by'] = $userId;
        $inputs['status_id'] = 1;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->create($inputs);

        if ($probationaryReview) {
            $message = 'Probation Review is added successfully.';

            return response()->json(['status' => 'ok',
                'message' => $message], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Probation Review can not be added.'], 422);
    }

    /**
     * Show the form for creating a new probation review.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('update', $probationaryReview);
        $employee = $this->employees->with('latestTenure')->find($probationaryReview->employee_id);
        $supervisors = $this->user->getSupervisors($employee->user);
        $approvers = $this->user->permissionBasedUsers('approve-probation-review-request');

        return view('ProbationaryReview::edit')
            ->with([
                'approvers' => ($approvers),
                'supervisors' => ($supervisors),
                'probationaryReview' => ($probationaryReview),
                'probationaryReviewTypes' => ($this->probationaryReviewType->get()),
            ]);

    }

    /**
     * Update probationary review in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function update(UpdateRequest $request, $id)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->update($id, $inputs);

        if ($probationaryReview) {
            if ($probationaryReview->status_id == config('constant.SUBMITTED_STATUS')) {
                $probationaryReview->reviewer->notify(new ProbationaryReviewRequestCreated($probationaryReview));
            }
            $message = 'Probation Review is successfully updated.';

            return response()->json(['status' => 'ok',
                'message' => $message], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Probation Review can not be added.'], 422);
    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('view', $probationaryReview);

        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
            ->where('probationary_review_id', $id)
            ->orderBy('probationary_indicator_id', 'asc')
            ->get();

        return view('ProbationaryReview::view')
            ->with([
                'employees' => ($this->employees->get()),
                'probationaryIndicators' => ($this->probationaryIndicator->get()),
                'probationaryReview' => ($this->probationaryReview->with('probationaryReviewIndicator')->find($id)),
                'probationaryReviewIndicators' => ($probationaryReviewIndicator),
                'probationaryReviewTypes' => ($this->probationaryReviewType->get()),
            ]);
    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendTo($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('sendTo', $probationaryReview);
        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
            ->where('probationary_review_id', $id)
            ->orderBy('probationary_indicator_id', 'asc')
            ->get();
        $approvers = $this->user->permissionBasedUsers('approve-probation-review-request');

        return view('ProbationaryReview::sendTo')
            ->with([
                'approvers' => ($approvers),
                'probationaryIndicators' => ($this->probationaryIndicator->get()),
                'probationaryReview' => ($this->probationaryReview->with('probationaryReviewIndicator')->find($id)),
                'probationaryReviewIndicators' => ($probationaryReviewIndicator),
                'probationaryReviewTypes' => ($this->probationaryReviewType->get()),
            ]);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendToStore(SendToRequest $request, $id)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $probationaryReview = $this->probationaryReview->find($id);
        $inputs['status_id'] = config('constant.VERIFIED2_STATUS');
        $inputs['updated_by'] = $userId;
        $probationaryReview = $this->probationaryReview->addApprover($inputs, $id);

        if ($probationaryReview) {
            $probationaryReview->approver->notify(new ProbationaryReviewRequestForApprove($probationaryReview));

            return redirect()->route('probation.review.requests.index')
                ->withSuccessMessage('Approver successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Approver can not be added.');
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('delete', $probationaryReview);
        $flag = $this->probationaryReview->destroy($id);

        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Probation Review Request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Probation Review Request can not deleted.',
        ], 422);
    }
}

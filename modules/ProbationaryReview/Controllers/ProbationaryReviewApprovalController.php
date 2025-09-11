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
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestApproved;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestApprovedEmployee;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestRejected;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestRejectedEmployee;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewIndicatorRepository;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewRepository;
use Modules\ProbationaryReview\Requests\ProbationaryReviewApprove\StoreRequest;

class ProbationaryReviewApprovalController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param  EmployeeRepository  $employees  ,
     * @param  ProbationaryReviewRepository  $probationaryReview  ,
     * @param  ProbationaryReviewTypeRepository  $probationaryReviewType  ,
     * @param  RoleRepository  $roles  ,
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
                ->where('status_id', config('constant.VERIFIED2_STATUS'))
                ->where('approver_id', $userId)
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
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.probation.review.request.create', $row->id).'" rel="tooltip" title="Approve"><i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('ProbationaryReview::ProbationaryReviewApprove.index');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('approve', $probationaryReview);

        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
            ->where('probationary_review_id', $id)
            ->orderBy('probationary_indicator_id', 'asc')
            ->get();

        return view('ProbationaryReview::ProbationaryReviewApprove.create')
            ->with([
                'probationaryIndicators' => ($this->probationaryIndicator->get()),
                'probationaryReview' => ($probationaryReview),
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
    public function store(StoreRequest $request, $id)
    {
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->find($id);
        $probationaryReview = $this->probationaryReview->approve($inputs, $id);
        if ($probationaryReview) {
            $probationaryReview = $this->probationaryReview->find($id);
            if ($probationaryReview->status_id == 8) {
                $message = 'Probationary Review request is successfully rejected.';
                $probationaryReview->createdBy->notify(new ProbationaryReviewRequestRejected($probationaryReview));
                $probationaryReview->employee->user->notify(new ProbationaryReviewRequestRejectedEmployee($probationaryReview));
            } else {
                $message = 'Probationary Review request is successfully approved.';
                $probationaryReview->createdBy->notify(new ProbationaryReviewRequestApproved($probationaryReview));
                $probationaryReview->employee->user->notify(new ProbationaryReviewRequestApprovedEmployee($probationaryReview));
            }

            return redirect()->route('approve.probation.review.requests.index')
                ->withSuccessMessage('Probation Review Status successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Probation Review Status can not be added.');
    }
}

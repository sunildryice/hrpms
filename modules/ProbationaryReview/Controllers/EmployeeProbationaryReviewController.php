<?php

namespace Modules\ProbationaryReview\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\ProbationaryIndicatorRepository;
use Modules\Master\Repositories\ProbationaryReviewTypeRepository;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestCommentAdded;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewRepository;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewIndicatorRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\ProbationaryReview\Requests\EmployeeProbationaryReview\StoreRequest;

use DataTables;
use DB;

class EmployeeProbationaryReviewController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     * @param EmployeeRepository $employees ,
     * @param ProbationaryReviewRepository $probationaryReview ,
     * @param ProbationaryReviewIndicatorRepository $probationaryReviewIndicator ,
     * @param ProbationaryReviewTypeRepository $probationaryReviewType ,
     * @param RoleRepository $roles ,
     * @param UserRepository $user
     *
     */
    public function __construct(
        protected EmployeeRepository                    $employees,
        protected ProbationaryIndicatorRepository       $probationaryIndicator,
        protected ProbationaryReviewRepository          $probationaryReview,
        protected ProbationaryReviewIndicatorRepository $probationaryReviewIndicator,
        protected ProbationaryReviewTypeRepository      $probationaryReviewType,
        protected RoleRepository                        $roles,
        protected UserRepository                        $user
    )
    {
        $this->destinationPath = 'probationaryReview';

    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $authUser = auth()->user();
        $employee_id = auth()->user()->employee_id;
        if ($request->ajax()) {
            $data = $this->probationaryReview
                ->select(['*'])
                ->where('employee_id', $employee_id)
                ->where('status_id', '!=', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('review_type', function ($row) {
                    return $row->getReviewType();
                })->addColumn('review_date', function ($row) {
                    return $row->getReviewDate();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('employeeRemarks', $row)) {
                        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('employeeProbation.review.detail.requests.create', $row->id) . '" rel="tooltip" title="Add Comment"><i class="bi-list-columns-reverse"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('ProbationaryReview::EmployeeProbationaryReview.index');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('employeeRemarks', $probationaryReview);
        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
            ->where('probationary_review_id', $id)
            ->orderBy('probationary_indicator_id', 'asc')
            ->get();
        return view('ProbationaryReview::EmployeeProbationaryReview.create')
            ->withAuthUser($authUser)
            ->withProbationaryReview($probationaryReview)
            ->withProbationaryReviewIndicators($probationaryReviewIndicator)
            ->withProbationaryReviewTypes($this->probationaryReviewType->get());

    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('employeeRemarks', $probationaryReview);

        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->addEmployeeRemarks($inputs, $id);

        if ($probationaryReview) {
            $probationaryReview->reviewer->notify(new ProbationaryReviewRequestCommentAdded($probationaryReview));
            return redirect()->route('employeeProbation.review.detail.requests.index')
                ->withSuccessMessage('Remarks successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Remarks can not be added.');
    }

}

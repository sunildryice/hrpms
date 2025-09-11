<?php
namespace Modules\ProbationaryReview\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\ProbationaryIndicatorRepository;
use Modules\Master\Repositories\ProbationaryReviewTypeRepository;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewRepository;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewIndicatorRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param ProbationaryIndicatorRepository $probationaryIndicator
     * @param ProbationaryReviewRepository $probationaryReview
     * @param ProbationaryReviewIndicatorRepository $probationaryReviewIndicator
     * @param ProbationaryReviewTypeRepository $probationaryReviewType
     * @param RoleRepository $roles
     * @param UserRepository $user
     */
    public function __construct(
        EmployeeRepository                        $employees,
        ProbationaryIndicatorRepository           $probationaryIndicator,
        ProbationaryReviewRepository              $probationaryReview,
        ProbationaryReviewIndicatorRepository     $probationaryReviewIndicator,
        ProbationaryReviewTypeRepository          $probationaryReviewType,
        RoleRepository                            $roles,
        UserRepository                            $user
    )
    {
        $this->employees                      = $employees;
        $this->probationaryIndicator          = $probationaryIndicator;
        $this->probationaryReview             = $probationaryReview;
        $this->probationaryReviewIndicator    = $probationaryReviewIndicator;
        $this->probationaryReviewType         = $probationaryReviewType;
        $this->roles                          = $roles;
        $this->user                           = $user;
        $this->destinationPath                = 'probationaryReview';
    }

    /**
     * Display a listing of the payment sheets
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
            $data = $this->probationaryReview->getApproved();
            return DataTables::of($data)
                ->addIndexColumn()->addColumn('employee_name', function ($row){
                    return $row->getEmployeeName();
                })->addColumn('review_type', function ($row){
                    return $row->getReviewType();
                })->addColumn('review_date', function ($row){
                    return $row->getReviewDate();
                })->addColumn('reviewer', function ($row){
                    return $row->getReviewerName();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.probation.review.request.show', $row->id) . '" rel="tooltip" title="View Probation Review">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approved.probation.review.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('ProbationaryReview::Approved.index');
    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('view', $probationaryReview);
        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
                                        ->where('probationary_review_id', $id)
                                        ->orderBy('probationary_indicator_id', 'asc')
                                        ->get();

        return view('ProbationaryReview::Approved.view')
            ->withEmployees($this->employees->get())
            ->withProbationaryIndicators($this->probationaryIndicator->get())
            ->withProbationaryReview($this->probationaryReview
                                        ->with('probationaryReviewIndicator')
                                        ->find($id))
            ->withProbationaryReviewIndicators($probationaryReviewIndicator)
            ->withProbationaryReviewTypes($this->probationaryReviewType->get());
    }

     /**
     * Show the specified probationary review in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('print', $probationaryReview);
        $probationaryReview = $this->probationaryReview ->select('*')
                                ->with('probationaryReviewIndicator', 'logs','createdBy','employee','reviewer','approver')
                                ->where('id', $id)
                                ->where('status_id', '6')
                                ->first();
        // $requester = $this->employees->select('*')->where('id', $probationaryReview->requester->employee_id)->first();
        $reviewer = $this->employees->select('*')->where('id', $probationaryReview->reviewer->employee_id)->first();
        // $approver = $this->employees->select('*')->where('id', $probationaryReview->approver->employee_id)->first();
        $date = array();
        foreach($probationaryReview->logs as $log){
            if($log->log_remarks == 'Employee comments added.' ){
                $date['comment_added_date'] = $log->created_at->toFormattedDateString();
            }
            if($log->log_remarks == 'Probation Review recommended.' ){
                $date['recommended_date'] = $log->created_at->toFormattedDateString();
            }
            if($log->status_id == 6 ){
                $date['approved_date'] = $log->created_at->toFormattedDateString();
            }

        }
        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
                                            ->where('probationary_review_id', $id)
                                            ->orderBy('probationary_indicator_id', 'asc')
                                            ->get();
        return view('ProbationaryReview::print')
            // ->withApprover($approver)
            ->withDates($date)
            // ->withRequester($requester)
            ->withReviewer($reviewer)
            ->withProbationaryIndicators($this->probationaryIndicator->get())
            ->withProbationaryReview($probationaryReview)
            ->withProbationaryReviewIndicators($probationaryReviewIndicator)
            ->withProbationaryReviewTypes($this->probationaryReviewType->get());
    }
}

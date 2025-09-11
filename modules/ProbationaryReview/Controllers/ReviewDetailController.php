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
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestRecommend;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestRecommended;
use Modules\ProbationaryReview\Notifications\ProbationaryReviewRequestReturned;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewIndicatorRepository;
use Modules\ProbationaryReview\Repositories\ProbationaryReviewRepository;
use Modules\ProbationaryReview\Requests\ReviewDetail\StoreRecommendRequest;
use Modules\ProbationaryReview\Requests\ReviewDetail\StoreRequest;

class ReviewDetailController extends Controller
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
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->probationaryReview->select(['*'])
                ->where('reviewer_id', $authUser->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addIndexColumn()->addColumn('review_type', function ($row) {
                    return $row->getReviewType();
                })->addIndexColumn()->addColumn('review_date', function ($row) {
                    return $row->getReviewDate();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('recommend', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('probation.review.detail.requests.recommend', $row->id).'" rel="tooltip" title="Recommend">';
                        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    } elseif ($authUser->can('review', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        if ($row->supervisor_recommendation != null) {
                            $btn .= route('probation.review.detail.requests.edit', $row->id).'" rel="tooltip" title="Edit Details">';
                            $btn .= '<i class="bi-pencil-square"></i></a>';
                        } else {
                            $btn .= route('probation.review.detail.requests.create', $row->id).'" rel="tooltip" title="Details">';
                            $btn .= '<i class="bi-pencil-square"></i></a>';
                        }
                    }

                    if ($authUser->can('view', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('probation.review.request.view', $row->id).'" rel="tooltip" title="Details"><i class="bi-eye"></i></a> ';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('ProbationaryReview::ReviewDetail.index');
    }

    /**
     * Show the form for add review detail by supervisor.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('review', $probationaryReview);

        return view('ProbationaryReview::ReviewDetail.create')
            ->with([
                'employees' => ($this->employees->get()),
                'probationaryIndicators' => ($this->probationaryIndicator->get()),
                'probationaryReview' => ($probationaryReview),
                'probationaryReviewIndicators' => ($probationaryReview->probationaryReviewIndicator),
                'probationaryReviewTypes' => ($this->probationaryReviewType->get()),
            ]);

    }

    /**
     * Store a newly add review details in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('review', $probationaryReview);

        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['objectives_met'] = array_key_exists('objectives_met', $inputs) ? 1 : 0;
        $inputs['development_addressed'] = array_key_exists('development_addressed', $inputs) ? 1 : 0;
        $inputs['appointment_confirmed'] = array_key_exists('appointment_confirmed', $inputs) ? 1 : 0;
        $inputs['probation_extended'] = array_key_exists('probation_extended', $inputs) ? 1 : 0;
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->review($inputs, $id);

        if ($probationaryReview) {
            if ($probationaryReview->status_id == '15') {
                $probationaryReview->employee->user->notify(new ProbationaryReviewRequestRecommended($probationaryReview));
            }

            return redirect()->route('probation.review.detail.requests.index')
                ->withSuccessMessage('Probation review request successfully added.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Probation review request can not be added.');
    }

    /**
     * Show the form for edit a review detail by supervisor.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('review', $probationaryReview);

        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
            ->where('probationary_review_id', $id)
            ->orderBy('probationary_indicator_id', 'asc')
            ->get();

        return view('ProbationaryReview::ReviewDetail.edit')
            ->with([
                'employees' => ($this->employees->get()),
                'probationaryReview' => ($probationaryReview),
                'probationaryReviewIndicators' => ($probationaryReviewIndicator),
            ]);

    }

    /**
     * Store a newly edit review detail in storage.
     *
     * @param  \Modules\Employee\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreRequest $request, $id)
    {
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('review', $probationaryReview);

        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['objectives_met'] = array_key_exists('objectives_met', $inputs) ? 1 : 0;
        $inputs['development_addressed'] = array_key_exists('development_addressed', $inputs) ? 1 : 0;
        $inputs['appointment_confirmed'] = array_key_exists('appointment_confirmed', $inputs) ? 1 : 0;
        $inputs['probation_extended'] = array_key_exists('probation_extended', $inputs) ? 1 : 0;
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->review($inputs, $id);

        if ($probationaryReview) {
            if ($probationaryReview->status_id == '15') {
                $probationaryReview->employee->user->notify(new ProbationaryReviewRequestRecommended($probationaryReview));
            } elseif ($probationaryReview->status_id == '4') {
                return redirect()->back()->withInput()
                    ->withSuccessMessage('Probation review updated.');
            }

            return redirect()->route('probation.review.detail.requests.index')
                ->withSuccessMessage('Probation review request successfully updated.');
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Probation review request can not be updated.');
    }

    /**
     * Show the form for creating a new travel report by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function recommend($id)
    {
        $authUser = auth()->user();
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('recommend', $probationaryReview);

        $probationaryReviewIndicator = $this->probationaryReviewIndicator->select('*')
            ->where('probationary_review_id', $id)
            ->orderBy('probationary_indicator_id', 'asc')
            ->get();

        return view('ProbationaryReview::ReviewDetail.recommend')
            ->with([
                'authUser' => ($authUser),
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
    public function storeRecommend(StoreRecommendRequest $request, $id)
    {
        $probationaryReview = $this->probationaryReview->find($id);
        $this->authorize('recommend', $probationaryReview);

        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $probationaryReview = $this->probationaryReview->recommend($inputs, $id);
        if ($probationaryReview) {
            $message = 'Probation Review successfully recommended.';
            if ($probationaryReview->status_id == '15') {
                $message = 'Probation review successfully returned';
                $probationaryReview->employee->user->notify(new ProbationaryReviewRequestReturned($probationaryReview));
            } else {
                $probationaryReview->createdBy->notify(new ProbationaryReviewRequestRecommend($probationaryReview));
            }

            return redirect()->route('probation.review.detail.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Probation Review can not be recommended.');
    }
}

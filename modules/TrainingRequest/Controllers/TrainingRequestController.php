<?php

namespace Modules\TrainingRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\TrainingRequest\Notifications\TrainingRequestSubmitted;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TrainingRequest\Requests\StoreDetailsRequest;
use Modules\TrainingRequest\Requests\StoreRequest;
use Modules\TrainingRequest\Requests\SendToRequest;

use DataTables;
use DB;

class TrainingRequestController extends Controller
{
    /**
     * Create a new controller instance.
     * @param AccountCodeRepository $accountCode ,
     * @param ActivityCodeRepository $activityCode ,
     * @param EmployeeRepository $employees ,
     * @param RoleRepository $roles ,
     * @param TrainingQuestionRepository $trainingQuestion ,
     * @param TrainingRequestRepository $trainingRequest ,
     * @param TrainingRequestQuestionRepository $trainingRequestQuestion ,
     * @param UserRepository $user
     *
     */
    public function __construct(
        AccountCodeRepository             $accountCode,
        ActivityCodeRepository            $activityCode,
        EmployeeRepository                $employees,
        RoleRepository                    $roles,
        TrainingQuestionRepository        $trainingQuestion,
        TrainingRequestRepository         $trainingRequest,
        TrainingRequestQuestionRepository $trainingRequestQuestion,
        UserRepository                    $user
    )
    {
        $this->accountCode = $accountCode;
        $this->activityCode = $activityCode;
        $this->employees = $employees;
        $this->roles = $roles;
        $this->trainingQuestion = $trainingQuestion;
        $this->trainingRequest = $trainingRequest;
        $this->trainingRequestQuestion = $trainingRequestQuestion;
        $this->user = $user;
        $this->destinationPath = 'trainingRequest';

    }

    /**
     * Display a listing of all the training request.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $this->authorize('training-request');
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->trainingRequest
            ->select(['*'])
            ->where(function ($query) use ($userId) {
                $query->where('created_by', $userId)
                    ->orWhere(function ($subQuery) use ($userId) {
                        $subQuery->where('approver_id', $userId)
                            ->whereNotIn('status_id', [config('constant.CREATED_STATUS')]);
                    })
                    ->orWhere(function ($subQuery) use ($userId) {
                        $subQuery->where('reviewer_id', $userId)
                            ->whereNotIn('status_id', [config('constant.CREATED_STATUS')]);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();
        

            return DataTables::of($data)
                ->addIndexColumn()->addColumn('training_number', function ($row) {
                    return $row->getTrainingRequestNumber();
                })->addColumn('name_of_course', function ($row) {
                    return $row->title;
                })->addIndexColumn()->addColumn('duration', function ($row) {
                    return $row->getDuration();
                })->addColumn('remarks', function ($row) {
                    return $row->description;
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('view', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.requests.view', $row->id) . '" rel="tooltip" title="View"><i class="bi-eye"></i></a> ';
                    }
                    if ($authUser->can('createReport', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.report.create', $row->id) . '" rel="tooltip" title="Create Training Report"><i class="bi-list-columns-reverse"></i></a> ';
                    }
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.requests.details', $row->id) . '" rel="tooltip" title="Edit Training Request"><i class="bi-pencil-square"></i></a> ';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('training.requests.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::index');
    }

    /**
     * Show the form for creating a new probation review.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $this->authorize('training-request');
        $activityCodes = $this->activityCode->getActiveActivityCodes();
        return view('TrainingRequest::create')
            ->withActivityCodes($activityCodes);

    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('training-request');
        $userId = auth()->id();
        $employee_id = auth()->user()->employee_id;
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $employee_id, time() . '_training.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['created_by'] = $userId;
        $inputs['status_id'] = 1;
        $trainingRequest = $this->trainingRequest->create($inputs);

        if ($trainingRequest) {
            return redirect()->route('training.requests.details', $trainingRequest->id)
                ->withSuccessMessage('Training Request is successfully added.');
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Training Request can not be added.');
    }

    /**
     * Show the form for creating a training request details form.
     *
     * @param $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function addDetails($id)
    {
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('update', $trainingRequest);
        $trainingRequestQuestion = $this->trainingRequestQuestion
            ->select('*')
            ->with('trainingQuestion')
            ->where('training_id', $id)
            ->orderBy('question_id', 'asc')
            ->get();
        $authUser = auth()->user();
        $reviewers = $this->user->permissionBasedUsers('hr-review-training-request');
        $reviewers = $reviewers->reject(function ($reviewer) use ($authUser) {
            return $authUser->id == $reviewer->id;
        });

        $recommenders = $this->user->getSupervisors($trainingRequest->requester);
        $approvers = collect();
        if ($trainingRequest->requester->can('self-approve-training-request')) {
            $approvers = $this->user->permissionBasedUsersInclusive('self-approve-training-request');
            $approvers = $approvers->reject(function ($approver) use ($trainingRequest) {
                return $approver->id != $trainingRequest->requester->id;
            });
        } else {
            $approvers = $this->user->permissionBasedUsers('approve-training-request');
            $approvers = $approvers->reject(function ($approver) use ($trainingRequest) {
                return $trainingRequest->created_by == $approver->id;
            });
        }

        return view('TrainingRequest::TrainingDetails.create')
            ->withAuthUser($authUser)
            ->withApprovers($approvers)
            ->withAccountCodes($this->accountCode->get())
            ->withActivityCodes($this->activityCode->getActiveActivityCodes())
            ->withReviewers($reviewers)
            ->withRecommenders($recommenders)
            ->withTrainingRequest($trainingRequest)
            ->withTrainingRequestQuestions($trainingRequestQuestion)
            ->withTrainingQuestions($this->trainingQuestion->where('type', '=', '1')->orderBy('position', 'asc')->get());

    }

    /**
     * Store a details of training in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeDetails(StoreDetailsRequest $request, $id)
    {
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('update', $trainingRequest);
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $trainingRequest = $this->trainingRequest->addDetails($inputs, $id);

        if ($trainingRequest) {
            $message = '';
            if ($trainingRequest->status_id == 3) {
                $trainingRequest->reviewer->notify(new TrainingRequestSubmitted($trainingRequest));
                $message = 'Training Request Details is successfully submitted.';
            } else {
                $message = 'Training Request Details is successfully saved.';
            }
            if($inputs['btn'] == 'save'){
                return redirect()->back()
                        ->withInput()
                        ->withSuccessMessage($message);        
            }
            return redirect()->route('training.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Training Request Details can not be edited.');
    }

    /**
     * Show the form for creating a new probation review.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('update', $trainingRequest);
        $authUser = auth()->user();
        $accountCodes = $trainingRequest->activityCode ?
            $trainingRequest->activityCode->accountCodes()->whereNotNull('activated_at')->orderBY('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCode->getActiveActivityCodes();
        return view('TrainingRequest::edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withtrainingRequest($trainingRequest);

    }

    /**
     * Store a newly created training request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreRequest $request, $id)
    {
        $this->authorize('training-request');
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $trainingRequest = $this->trainingRequest->update($inputs, $id);

        if ($trainingRequest) {
            return redirect()->route('training.requests.details', $trainingRequest->id)
                ->withSuccessMessage('Training Request is successfully updated.');
        }
        return redirect()->route('training.requests.details', $trainingRequest->id)
            ->withInput()
            ->withWarningMessage('Training Request can not be updated.');
    }

    /**
     * View the details the specified Training request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($id)
    {
//        $this->authorize('manage-employee');
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('view', $trainingRequest);
        if ($trainingRequest) {
            $trainingRequestQuestions = $this->trainingRequestQuestion
                ->select('*')
                ->with('trainingQuestion')
                ->where('training_id', $id)
                ->orderBy('question_id', 'asc')
                ->get();
            $count = 0;
            foreach ($trainingRequestQuestions as $trainingRequestQuestion) {
                if ($trainingRequestQuestion->trainingQuestion->answer_type == 'textarea' && $trainingRequestQuestion->trainingQuestion->type == '3') {
                    $count++;
                }
            }
            return view('TrainingRequest::view')
                ->withHrResponseCount($count)
                ->withTrainingRequest($trainingRequest)
                ->withTrainingRequestQuestions($trainingRequestQuestions);
        }
    }

    /**
     * Remove the specified travel request from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
//        $this->authorize('manage-employee');
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('delete', $trainingRequest);
        $flag = $this->trainingRequest->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Training Request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Training Request can not deleted.',
        ], 422);
    }

}

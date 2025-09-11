<?php
namespace Modules\TrainingRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;

class ApprovedTrainingRequestController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository                $employees,
     * @param RoleRepository                    $roles,
     * @param TrainingQuestionRepository        $trainingQuestion,
     * @param TrainingRequestRepository         $trainingRequest,
     * @param TrainingRequestQuestionRepository $trainingRequestQuestion,
     * @param UserRepository                    $user
     *
     */
    public function __construct(
        EmployeeRepository                $employees,
        RoleRepository                    $roles,
        TrainingQuestionRepository        $trainingQuestion,
        TrainingRequestRepository         $trainingRequest,
        TrainingRequestQuestionRepository $trainingRequestQuestion,
        UserRepository                    $user
    )
    {
        $this->employees                  = $employees;
        $this->roles                      = $roles;
        $this->trainingQuestion           = $trainingQuestion;
        $this->trainingRequest            = $trainingRequest;
        $this->trainingRequestQuestion    = $trainingRequestQuestion;
        $this->user                       = $user;
        $this->destinationPath            = 'trainingRequest';
    }

    /**
     * Display a listing of the payment sheets
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->trainingRequest
                        ->select(['*'])
                        ->where('status_id', '6')
                        // ->where(function ($q) use ($userId) {
                        //    $q->where('reviewer_id', $userId);
                        //    $q->orwhere('recommender_id', $userId);
                        //    $q->orwhere('approver_id', $userId);
                        //    $q->orwhere('created_by', $userId);
                        // })
                        ->orderBy('created_at', 'desc')
                        ->get();
            return DataTables::of($data)
                ->addIndexColumn()->addColumn('training_number', function ($row){
                    return $row->getTrainingRequestNumber();
                })->addColumn('requester', function ($row){
                    return $row->getRequesterName();
                })->addColumn('name_of_course', function ($row){
                    return $row->title;
                })->addIndexColumn()->addColumn('duration', function ($row){
                    return $row->getDuration();
                })->addColumn('remarks', function ($row){
                    return $row->description;
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.training.request.show', $row->id) . '" rel="tooltip" title="View Training Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if($authUser->can('print', $row) || $authUser->can('hr-review-training-request')) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.request.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::Approved.index');
    }

    /**
     * Show the specified payment sheet.
     *
     * @param $paymentRequestId
     * @return mixed
     */
    public function show($id)
    {
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('viewApproved', $trainingRequest);
        $trainingRequestQuestion = $this->trainingRequestQuestion
                                        ->select('*')
                                        ->with('trainingQuestion')
                                        ->where('training_id', $id)
                                        ->orderBy('question_id','asc')
                                        ->get();
        return view('TrainingRequest::Approved.show')
            ->withTrainingRequest($trainingRequest)
            ->withTrainingRequestQuestions($trainingRequestQuestion);
    }

     /**
     * Show the specified training request in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('print', $trainingRequest);
        $trainingRequest = $this->trainingRequest ->select('*')
                                ->with('logs','requester','recommender','approver')
                                ->where('id', $id)
                                ->where('status_id', '6')
                                ->first();
        $requester = $this->employees->select('*')->where('id', $trainingRequest->requester->employee_id)->first();
        $recommender = $this->employees->select('*')->where('id', $trainingRequest->recommender->employee_id)->first();
        $approver = $this->employees->select('*')->where('id', $trainingRequest->approver->employee_id)->first();
        $date = array();
        foreach($trainingRequest->logs as $log){
            if($log->status_id == 3 ){
                $date['submitted_date'] = $log->created_at;
            }
            if($log->status_id == 11 ){
                $date['reviewed_date'] = $log->created_at;
            }
            if($log->status_id == 4 || $log->status_id == 5){
                $date['recommended_date'] = $log->created_at;
            }
            if($log->status_id == 6 ){
                $date['approved_date'] = $log->created_at;
            }

        }
        $trainingRequestQuestion = $this->trainingRequestQuestion
                                        ->select('*')
                                        ->with('trainingQuestion')
                                        ->where('training_id', $id)
                                        ->orderBy('question_id','asc')
                                        ->get();
        return view('TrainingRequest::print')
            ->withApprover($approver)
            ->withDates($date)
            ->withRequester($requester)
            ->withRecommender($recommender)
            ->withTrainingRequest($trainingRequest)
            ->withTrainingRequestQuestions($trainingRequestQuestion);
    }
}

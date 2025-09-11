<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\ExitInterview\ExitInterviewApproved;
use Modules\EmployeeExit\Notifications\ExitInterview\ExitInterviewRecommended;
use Modules\EmployeeExit\Notifications\ExitInterview\ExitInterviewReturned;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRatingAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewFeedBackAnswerRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeExit\Requests\ExitInterview\Approve\StoreRequest;
use DataTables;

class ExitInterviewApproveController extends Controller
{

    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository     $employees,
     * @param ExitHandOverNoteRepository     $exitHandOverNote,
     * @param ExitInterviewAnswerRepository     $exitInterviewAnswer,
     * @param ExitInterviewFeedBackAnswerRepository     $exitInterviewFeedbackAnswers,
     * @param ExitInterviewRatingAnswerRepository     $exitInterviewRatingAnswers,
     * @param ExitInterviewRepository     $exitInterview,
     * @param UserRepository         $users
     */
    public function __construct(
        protected EmployeeRepository             $employees,
        protected ExitHandOverNoteRepository     $exitHandOverNote,
        protected ExitInterviewAnswerRepository  $exitInterviewAnswer,
        protected ExitInterviewFeedBackAnswerRepository     $exitInterviewFeedbackAnswers,
        protected ExitInterviewRatingAnswerRepository       $exitInterviewRatingAnswers,
        protected ExitInterviewRepository        $exitInterview,
        protected UserRepository                 $users
    )
    {
        $this->destinationPath = 'employeeExit';
    }
    /**
     * Display a listing of the exit interview
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->exitInterview->with(['employee', 'status'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                });
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row){
                    return $row->getEmployeeName();
                })->addColumn('last_duty_date', function ($row){
                    return $row->exitHandOverNote->getLastDutyDate();
                })->addColumn('resignation_date', function ($row){
                    return $row->exitHandOverNote->getResignationDate();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    if($authUser->can('approve', $row)) {
                        $btn = '<a href = "'.route('approve.exit.interview.create', $row->id).'" class="btn btn-secondary btn-sm"  rel="tooltip" title="Approve Interview">';
                        $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::ExitInterview.Approve.index');

    }

    /**
     * Show the form for creating a new hand over note by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id){
        $authUser = auth()->user();
        $exitInterview = $this->exitInterview->find($id);

        $this->authorize('approve', $exitInterview);

        $exitInterviewFeedbackAnswers = $this->exitInterviewFeedbackAnswers->where('exit_interview_id','=',$exitInterview->id)->get();
        $exitInterviewRatingAnswers = $this->exitInterviewRatingAnswers->where('exit_interview_id','=',$exitInterview->id)->get();
        $exitInterviewQuestionAnswers = $this->exitInterviewAnswer->where('exit_interview_id','=',$exitInterview->id)->get();
        $approvers = $this->users->permissionBasedUsers('approve-exit-interview');
        // $this->authorize('approve', $exitInterview);
        return view('EmployeeExit::ExitInterview.Approve.create')
            ->withAuthUser(auth()->user())
            ->withApprovers($approvers)
            ->withExitInterview($exitInterview)
            ->withExitInterviewFeedbackAnswers($exitInterviewFeedbackAnswers)
            ->withExitInterviewRatingAnswers($exitInterviewRatingAnswers)
            ->withExitInterviewQuestionAnswers($exitInterviewQuestionAnswers);
    }


    /**
     * Store a newly created advance request in storage.
     *
     * @param \Modules\EmployeeExit\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $exitInterview = $this->exitInterview->find($id);

        $this->authorize('approve', $exitInterview);

        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitInterview = $this->exitInterview->approve($exitInterview->id, $inputs);
        if ($exitInterview) {
            $message = '';
            if ($exitInterview->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Exit Interview is successfully returned.';
                $exitInterview->employee->user->notify(new ExitInterviewReturned($exitInterview));
            }else if ($exitInterview->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Exit Interview is successfully recommended.';
                $exitInterview->approver->notify(new ExitInterviewRecommended($exitInterview));
            } else {
                $message = 'Exit Interview is successfully approved.';
                $exitInterview->employee->user->notify(new ExitInterviewApproved($exitInterview));
            }
            return redirect()->route('approve.exit.interview.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Exit Interview can not be approved.');
    }
}

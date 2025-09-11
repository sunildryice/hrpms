<?php

namespace Modules\EmployeeExit\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Modules\EmployeeExit\Repositories\ExitInterviewAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewFeedBackAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRatingAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;

class ExitInterviewApprovedController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param ExitInterviewRepository $exitInterview
     * @param ExitInterviewAnswerRepository $exitInterviewAnswer
     * @param ExitInterviewRatingAnswerRepository $exitInterviewRatingAnswers
     * @param ExitInterviewFeedBackAnswerRepository $exitInterviewFeedbackAnswers
     * @param UserRepository $users
     */
    public function __construct(
        protected ExitInterviewRepository               $exitInterview,
        protected ExitInterviewAnswerRepository         $exitInterviewAnswer,
        protected ExitInterviewRatingAnswerRepository   $exitInterviewRatingAnswers,
        protected ExitInterviewFeedBackAnswerRepository $exitInterviewFeedbackAnswers,
        protected UserRepository                        $users
    )
    {
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
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.CLOSED_STATUS')]);
                })->orderBy('created_at', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('last_duty_date', function ($row) {
                    return $row->exitHandOverNote->getLastDutyDate();
                })->addColumn('resignation_date', function ($row) {
                    return $row->exitHandOverNote->getResignationDate();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.exit.interview.show', $row->id) . '" rel="tooltip" title="View Exit Interview"><i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a href = "' . route('exit.employee.interview.print', $row->id) . '" target="_blank" class="btn btn-outline-primary btn-sm" rel="tooltip" title="Print Exit Interview">';
                    $btn .= '<i class="bi bi-printer"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('EmployeeExit::ExitInterview.Approved.index');
    }

    public function show(Request $request, $id)
    {
        $exitInterview = $this->exitInterview->find($id);
        $exitInterviewFeedbackAnswers = $this->exitInterviewFeedbackAnswers->where('exit_interview_id', '=', $exitInterview->id)->get();
        $exitInterviewRatingAnswers = $this->exitInterviewRatingAnswers->where('exit_interview_id', '=', $exitInterview->id)->get();
        $exitInterviewQuestionAnswers = $this->exitInterviewAnswer->where('exit_interview_id', '=', $exitInterview->id)->get();

        return view('EmployeeExit::ExitInterview.Approved.show')
            ->withExitHandOverNote($exitInterview->exitHandOverNote)
            ->withExitInterview($exitInterview)
            ->withExitInterviewFeedbackAnswers($exitInterviewFeedbackAnswers)
            ->withExitInterviewRatingAnswers($exitInterviewRatingAnswers)
            ->withExitInterviewQuestionAnswers($exitInterviewQuestionAnswers);
    }
}

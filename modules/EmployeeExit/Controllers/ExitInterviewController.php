<?php

namespace Modules\EmployeeExit\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Notifications\ExitInterview\ExitInterviewSubmitted;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewRatingAnswerRepository;
use Modules\EmployeeExit\Repositories\ExitInterviewFeedBackAnswerRepository;
use Modules\Master\Repositories\ExitFeedbackRepository;
use Modules\Master\Repositories\ExitRatingRepository;
use Modules\Master\Repositories\ExitQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\EmployeeExit\Requests\ExitInterview\StoreRequest;
use Modules\EmployeeExit\Requests\ExitInterview\UpdateRequest;

use DataTables;

class ExitInterviewController extends Controller
{

    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository     $employees,
     * @param ExitHandOverNoteRepository     $exitHandOverNote,
     * @param ExitInterviewAnswerRepository     $exitInterviewAnswer,
     * @param ExitInterviewRatingAnswerRepository     $exitInterviewRatingAnswers,
     * @param ExitInterviewFeedBackAnswerRepository     $exitInterviewFeedbackAnswers,
     * @param ExitInterviewRepository     $exitInterview,
     * @param ExitQuestionRepository     $exitQuestions,
     * @param ExitRatingRepository     $exitRatings,
     * @param ExitFeedbackRepository     $exitFeedbacks,
     * @param UserRepository         $users
     */
    public function __construct(
        protected EmployeeRepository     $employees,
        protected ExitHandOverNoteRepository     $exitHandOverNote,
        protected ExitInterviewAnswerRepository     $exitInterviewAnswer,
        protected ExitInterviewRatingAnswerRepository     $exitInterviewRatingAnswers,
        protected ExitInterviewFeedBackAnswerRepository     $exitInterviewFeedbackAnswers,
        protected ExitInterviewRepository     $exitInterview,
        protected ExitQuestionRepository     $exitQuestions,
        protected ExitRatingRepository     $exitRatings,
        protected ExitFeedbackRepository     $exitFeedbacks,
        protected UserRepository         $users
    )
    {
        $this->destinationPath = 'employeeExit';
    }

    /**
     * Show the specified exit interview.
     *
     * @param $advanceRequestId
     * @return mixed
     */
    public function show()
    {
        $authUser = auth()->user();
        $exitInterview = $this->exitInterview->where('employee_id','=',$authUser->employee_id)->first();
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id','=',$authUser->employee_id)->first();
        $exitAssetHandover = $exitHandOverNote->exitAssetHandover;
        $exitInterviewFeedbackAnswers = $this->exitInterviewFeedbackAnswers->where('exit_interview_id','=',$exitInterview->id)->get();
        $exitInterviewRatingAnswers = $this->exitInterviewRatingAnswers->where('exit_interview_id','=',$exitInterview->id)->get();
        $exitInterviewQuestionAnswers = $this->exitInterviewAnswer->where('exit_interview_id','=',$exitInterview->id)->get();

        return view('EmployeeExit::ExitInterview.show')
            ->withAuthUser(auth()->user())
            ->withExitHandOverNote($exitHandOverNote)
            ->withExitInterview($exitInterview)
            ->withExitInterviewFeedbackAnswers($exitInterviewFeedbackAnswers)
            ->withExitInterviewRatingAnswers($exitInterviewRatingAnswers)
            ->withExitAssetHandover($exitAssetHandover)
            ->withExitInterviewQuestionAnswers($exitInterviewQuestionAnswers);
    }


    /**
     * Show the form for editing the specified advance request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit()
    {
        $authUser = auth()->user();
        $exitInterview = $this->exitInterview->where('employee_id','=',$authUser->employee_id)->first();
        $this->authorize('update', $exitInterview);
        $exitHandOverNote = $this->exitHandOverNote->where('employee_id','=',$authUser->employee_id)->first();
        $exitAssetHandover = $exitHandOverNote->exitAssetHandover;
        $supervisors = $this->users->getSupervisors($authUser);
        $approvers = $this->users->permissionBasedUsers('approve-exit-interview');
        $exitQuestions = $this->exitQuestions->get();
        $exitRatings = $this->exitRatings->get();
        $exitFeedbacks = $this->exitFeedbacks->get();
        $exitInterviewFeedbackAnswers = $this->exitInterviewFeedbackAnswers->where('exit_interview_id','=',$exitInterview->id)->get();
        $exitInterviewRatingAnswers = $this->exitInterviewRatingAnswers->where('exit_interview_id','=',$exitInterview->id)->get();
        $exitInterviewQuestionAnswers = $this->exitInterviewAnswer->where('exit_interview_id','=',$exitInterview->id)->get();

        return view('EmployeeExit::ExitInterview.edit')
            ->withApprovers($approvers)
            ->withAuthUser(auth()->user())
            ->withExitQuestions($exitQuestions)
            ->withExitHandOverNote($exitHandOverNote)
            ->withExitRatings($exitRatings)
            ->withExitFeedbacks($exitFeedbacks)
            ->withExitInterview($exitInterview)
            ->withExitInterviewFeedbackAnswers($exitInterviewFeedbackAnswers)
            ->withExitInterviewRatingAnswers($exitInterviewRatingAnswers)
            ->withExitInterviewQuestionAnswers($exitInterviewQuestionAnswers)
            ->withExitAssetHandover($exitAssetHandover)
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified advance request in storage.
     *
     * @param \Modules\AdvanceRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreRequest $request,$id)
    {
        $inputs = $request->validated();
        $exitInterview = $this->exitInterview->where('employee_id','=',$id)->first();
        $req_boolean = $request->boolean ?? array();
        $req_textarea = $request->textarea ?? array();
        $req_checkbox = $request->checkbox ?? array();
        $keys = array_merge(array_keys($req_boolean), array_keys($req_textarea), array_keys($req_checkbox));
        $vals = array_merge($req_boolean, $req_textarea,$req_checkbox);
        $questionAnswer = array_combine($keys, $vals);
        $ratingAnswers = $request->ratingAnswers ?? array();
        $feedbackAnswers = $request->feedbackAnswers ?? array();
        foreach($questionAnswer as $key=>$value){
            $inputs['exit_interview_id'] = $exitInterview->id;
            $inputs['question_id'] = $key;
            $inputs['answer'] = $value;
            $exitInterviewAnswer = $this->exitInterviewAnswer->updateOrCreate($inputs);
        }
        foreach($feedbackAnswers as $key=>$value1){
            $inputs['exit_interview_id'] = $exitInterview->id;
            $inputs['exit_feedback_id'] = $key;
            $inputs['always'] = 0;
            $inputs['almost'] = 0;
            $inputs['usually'] = 0;
            $inputs['sometimes'] = 0;
            if($value1 == 'always'){
                $inputs['always'] = 1;
            }elseif ($value1 == 'almost') {
                $inputs['almost'] = 1;
            }elseif ($value1 == 'usually') {
                $inputs['usually'] = 1;
            }elseif ($value1 == 'sometimes') {
                $inputs['sometimes'] = 1;
            }

            $exitInterviewFeedback = $this->exitInterviewFeedbackAnswers->updateOrCreate($inputs);
        }
        foreach($ratingAnswers as $key=>$value1){
            $inputs['exit_interview_id'] = $exitInterview->id;
            $inputs['exit_rating_id'] = $key;
            $inputs['excellent'] = 0;
            $inputs['good'] = 0;
            $inputs['fair'] = 0;
            $inputs['poor'] = 0;
            if($value1 == 'excellent'){
                $inputs['excellent'] = 1;
            }elseif ($value1 == 'good') {
                $inputs['good'] = 1;
            }elseif ($value1 == 'fair') {
                $inputs['fair'] = 1;
            }elseif ($value1 == 'poor') {
                $inputs['poor'] = 1;
            }
            $exitInterviewRating = $this->exitInterviewRatingAnswers->updateOrCreate($inputs);
        }
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $exitInterview = $this->exitInterview->update($exitInterview->id, $inputs);
        if ($exitInterview) {
            $message = 'Exit Interview is successfully updated.';
            if ($exitInterview->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Exit HandOver Note is successfully submitted.';
                $exitInterview->approver->notify(new ExitInterviewSubmitted($exitInterview));
                return redirect()->route('exit.employee.interview.show')
                ->withSuccessMessage($message);
            }
            return redirect()->route('exit.employee.interview.edit')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Exit Interview can not be updated.');
    }

    /**
     * Remove the specified advance request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $exitHandOverNote = $this->exitHandOverNote->find($id);
        // $this->authorize('delete', $exitHandOverNote);
        $flag = $this->exitHandOverNote->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit HandOver request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit HandOver request can not deleted.',
        ], 422);
    }


    public function print($id)
    {
        $data = $this->exitInterview->select('*')->findOrFail($id);

        return view('EmployeeExit::ExitInterview.print', compact('data'));
    }

}

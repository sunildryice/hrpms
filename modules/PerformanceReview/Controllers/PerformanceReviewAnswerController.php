<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Repositories\PerformanceReviewAnswerRepository;

class PerformanceReviewAnswerController extends Controller
{
    public function __construct(
        PerformanceReviewRepository $performanceReview,
        PerformanceReviewQuestion $performanceReviewQuestion,
        PerformanceReviewAnswerRepository $performanceReviewAnswer
    )
    {
        $this->performanceReview = $performanceReview;
        $this->performanceReviewQuestion = $performanceReviewQuestion;
        $this->performanceReviewAnswer = $performanceReviewAnswer;        
    }

    public function store(Request $request)
    {
        $inputs = array(
            'performance_review_id' => $request->performance_review_id,
            'question_id'           => $request->question_id,
            'answer'                => $request->answer
        );
        
        $performanceReviewAnswer = $this->performanceReviewAnswer
                                        ->where('performance_review_id', '=', $request->performance_review_id)
                                        ->where('question_id', '=', $request->question_id)
                                        ->first();

        if ($performanceReviewAnswer) {
            $performanceReviewAnswer = $this->performanceReviewAnswer->update($performanceReviewAnswer->id, $inputs);
        } else {
            $performanceReviewAnswer = $this->performanceReviewAnswer->create($inputs);
        }

        if ($performanceReviewAnswer) {
            return response()->json(['type' => 'success', 'message' => 'Answer Saved.'], 200);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Answer could not be saved.'], 422);
        }
    }
    public function storeMany(Request $request)
    {
        $performanceReviewId = $request->performance_review_id;
        $answersData = $request->data;

        foreach ($answersData as $answerData) {
            $questionId = $answerData['question_id'];
            $answer = $answerData['answer'];

            $performanceReviewAnswer = $this->performanceReviewAnswer
                ->where('performance_review_id','=', $performanceReviewId)
                ->where('question_id','=', $questionId)
                ->first();

            $inputs = [
                'performance_review_id' => $performanceReviewId,
                'question_id' => $questionId,
                'answer' => $answer,
            ];

            if ($performanceReviewAnswer) {
                $performanceReviewAnswer->update($inputs);
            } else {
                $this->performanceReviewAnswer->create($inputs);
            }
        }

        $savedAnswer = $this->performanceReviewAnswer->where('performance_review_id', '=', $performanceReviewId)
                        ->where('answer','=', 'true')
                        ->first();

        return response()->json(['type' => 'success', 'message' => 'Answers saved.',
        'question' => $savedAnswer->performanceReviewQuestion->question
    ], 200);
    }

    public function get(Request $request)
    {
        $performanceReviewAnswer = $this->performanceReviewAnswer
                                        ->where('performance_review_id', '=', $request->performance_review_id)
                                        ->where('question_id', '=', $request->question_id)
                                        ->first();
        $answer = '';
        if ($performanceReviewAnswer) {
            $answer = $performanceReviewAnswer->answer;
            return response()->json(['type' => 'success', 'answer' => $answer], 200);
        } else {
            return response()->json(['type' => 'success', 'answer' => $answer], 422);
        }
    }
}
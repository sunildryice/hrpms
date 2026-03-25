<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewAnswer;
use Modules\PerformanceReview\Models\PerformanceReviewQuestion;
use Modules\PerformanceReview\Repositories\PerformanceReviewKeyGoalRepository;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;

class PerformanceReviewKeyGoalController extends Controller
{
    public function __construct(
        PerformanceReviewRepository $performanceReview,
        PerformanceReviewKeyGoalRepository $performanceReviewKeyGoal,
        protected PerformanceReviewQuestion $performanceReviewQuestion,
    ) {
        $this->performanceReview = $performanceReview;
        $this->performanceReviewKeyGoal = $performanceReviewKeyGoal;
        $this->performanceReviewQuestion = $performanceReviewQuestion;
    }

    public function store(Request $request)
    {
        $inputs = array(
            'performance_review_id' => $request->performance_review_id,
            'title' => $request->title,
            'output_deliverables'   => $request->output_deliverables,
            'description_employee' => $request->description_employee,
            'description_supervisor' => $request->description_supervisor,
            'type' => $request->type,
        );

        $performanceReviewKeyGoal = $this->performanceReviewKeyGoal->create($inputs);

        if ($performanceReviewKeyGoal) {
            return response()->json(['type' => 'success', 'message' => 'Key Goal Saved.'], 200);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Key Goal could not be saved.'], 422);
        }
    }

    public function edit(Request $request)
    {
        $inputs = array(
            'title' => $request->title,
        );

        $performanceReviewKeyGoal = $this->performanceReviewKeyGoal->update($request->keyGoalId, $inputs);

        if ($performanceReviewKeyGoal) {
            return response()->json(['type' => 'success', 'message' => 'Key Goal updated.'], 200);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Key Goal could not be updated.'], 422);
        }
    }

    public function append(Request $request)
    {
        $inputs = array(
            'performance_review_id' => $request->performance_review_id,
            'type' => 'current'
        );

        $performanceReviewKeyGoal = $this->performanceReviewKeyGoal->create($inputs);

        if ($performanceReviewKeyGoal) {
            $html = '<tr>';
            $html .= '<td>';
            $html .= '<input style="width: 100%" type="text" name="keygoal_title_' . $performanceReviewKeyGoal->id . '" id="keygoal_title_' . $performanceReviewKeyGoal->id . '">';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<input style="width: 100%" type="text" name="keygoal_employee_' . $performanceReviewKeyGoal->id . '" id="keygoal_employee_' . $performanceReviewKeyGoal->id . '">';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<input disabled style="width: 100%" type="text" name="keygoal_supervisor_' . $performanceReviewKeyGoal->id . '" id="keygoal_supervisor_' . $performanceReviewKeyGoal->id . '">';
            $html .= '</td>';
            $html .= '<td class="text-center">';
            $html .= '<a role="button" onclick="removeGoal(event,' . $performanceReviewKeyGoal->id . ')" class="link-danger" rel="tooltip" title="Remove"><i class="bi bi-trash"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';

            return response()->json(['type' => 'success', 'html' => $html], 200);
        } else {
            return response()->json(['type' => 'success', 'html' => ''], 422);
        }
    }

    // public function update(Request $request)
    // {
    //     $inputs = array();

    //     if ($request->title != '') {
    //         $inputs['title'] = $request->title;
    //     }

    //     if ($request->description_employee != '') {
    //         $inputs['description_employee'] = $request->description_employee;
    //     }

    //     if ($request->description_supervisor != '') {
    //         $inputs['description_supervisor'] = $request->description_supervisor;
    //     }

    //     if ($request->description_employee_annual != '') {
    //         $inputs['description_employee_annual'] = $request->description_employee_annual;
    //     }

    //     if ($request->description_supervisor_annual != '') {
    //         $inputs['description_supervisor_annual'] = $request->description_supervisor_annual;
    //     }

    //     if ($request->type != '') {
    //         $inputs['type'] = $request->type;
    //     }

    //     $performanceReviewKeyGoal = $this->performanceReviewKeyGoal->find($request->key_goal_id);

    //     if ($performanceReviewKeyGoal) {
    //         $performanceReviewKeyGoal = $this->performanceReviewKeyGoal->update($request->key_goal_id, $inputs);
    //     } else {
    //         $inputs['performance_review_id'] = $request->performance_review_id;
    //         $performanceReviewKeyGoal = $this->performanceReviewKeyGoal->create($inputs);
    //     }


    //     if ($performanceReviewKeyGoal) {
    //         return response()->json(['type' => 'success', 'message' => 'Key Goal Saved.'], 200);
    //     } else {
    //         return response()->json(['type' => 'success', 'message' => 'Key Goal could not be Saved.'], 422);
    //     }
    // }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'key_goal_id' => 'required|exists:performance_review_key_goals,id',
            'title' => 'nullable|string',
            'major_activities_employee' => 'nullable|string',
            'description_supervisor_annual' => 'nullable|string',
            'status' => 'nullable|in:not_completed,partially_completed,fully_completed',
            'remarks_employee' => 'nullable|string',
            'type' => 'nullable|in:current,future',
            'performance_review_id' => 'nullable|exists:performance_reviews,id',
        ]);

        $keyGoal = $this->performanceReviewKeyGoal->find($request->key_goal_id);

        $keyGoal->update([
            'title' => $request->title ?? $keyGoal->title,
            'major_activities_employee' => $request->major_activities_employee ?? $keyGoal->major_activities_employee,
            'description_supervisor_annual' => $request->description_supervisor_annual ?? $keyGoal->description_supervisor_annual,
            'output_deliverables' => $request->output_deliverables ?? $keyGoal->output_deliverables,
            'status' => $request->status ?? $keyGoal->status,
            'remarks_employee' => $request->remarks_employee ?? $keyGoal->remarks_employee,
            'type' => $request->type ?? $keyGoal->type,
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Key Goal updated successfully.'
        ]);
    }

    public function getKeyGoalsEmployee(Request $request)
    {
        $performanceReview = $this->performanceReview->find($request->performance_review_id);

        $performanceReviewKeyGoals = $this->performanceReviewKeyGoal
            ->where('performance_review_id', '=', $request->performance_review_id)
            ->where('created_by', '=', $performanceReview->employee->user->id)
            ->where('type', '=', 'future')
            ->get();

        if ($performanceReviewKeyGoals) {
            $list = '<ol>';
            foreach ($performanceReviewKeyGoals as $keygoal) {
                $list .= '<li>' . $keygoal->title;
                if ($keygoal->created_by == auth()->user()->id) {
                    $list .= '&emsp;<button role="button" class="btn btn-sm open-edit-modal-form" rel="tooltip" title="Edit" href="';
                    $list .= route('performance.keygoal.editOne', $keygoal->id) . '" ><i class="bi bi-pencil-square"></i></button>';
                    $list .= '&emsp;<a role="button" class="link-danger" rel="tooltip" title="Remove" onclick="deleteKeyGoal(' . $keygoal->id . ')"><i class="bi bi-trash"></i></a>';
                }
                $list .= '</li>';
            }
            $list .= '</ol>';

            return response()->json(['type' => 'success', 'goal' => $list], 200);
        } else {
            return response()->json(['type' => 'success', 'goal' => ''], 422);
        }
    }

    public function getKeyGoalsSupervisor(Request $request)
    {
        $performanceReview = $this->performanceReview->find($request->performance_review_id);

        $performanceReviewKeyGoals = $this->performanceReviewKeyGoal
            ->where('performance_review_id', '=', $request->performance_review_id)
            ->where('type', '=', 'future')
            ->where('created_by', '!=', $performanceReview->employee->user->id)
            ->get();

        if ($performanceReviewKeyGoals) {
            $list = '<ol>';
            foreach ($performanceReviewKeyGoals as $keygoal) {
                $list .= '<li>' . $keygoal->title;
                if ($keygoal->created_by == auth()->user()->id) {
                    $list .= '&emsp;<button role="button" class="btn btn-sm open-edit-modal-form" rel="tooltip" title="Edit" href="';
                    $list .= route('performance.keygoal.editOne', $keygoal->id) . '" ><i class="bi bi-pencil-square"></i></button>';
                    $list .= '&emsp;<a role="button" class="link-danger" rel="tooltip" title="Remove" onclick="deleteKeyGoal(' . $keygoal->id . ')"><i class="bi bi-trash"></i></a>';
                }
                $list .= '</li>';
            }
            $list .= '</ol>';

            return response()->json(['type' => 'success', 'goal' => $list], 200);
        } else {
            return response()->json(['type' => 'success', 'goal' => ''], 422);
        }
    }

    public function getEmployeeCurrentKeyGoals(Request $request)
    {
        $performanceReview = $this->performanceReview->find($request->performance_review_id);

        $performanceReviewKeyGoals = $this->performanceReviewKeyGoal
            ->where('performance_review_id', '=', $request->performance_review_id)
            ->where('created_by', '=', $performanceReview->employee->user->id)
            ->where('type', '=', 'current')
            ->get();

        if ($performanceReviewKeyGoals) {
            $list = '<ol>';
            foreach ($performanceReviewKeyGoals as $keygoal) {
                $list .= '<li>';
                $list .= '<span id="keygoal-title">' . $keygoal->title . '</span>';
                if ($keygoal->created_by == auth()->user()->id) {
                    $list .= '&emsp;<a role="button" class="link-primary" rel="tooltip" title="Edit" onclick="editKeyGoal(event, ' . $keygoal->id . ')"><i class="bi bi-pencil-square"></i></i></a>';
                    $list .= '&emsp;<a role="button" class="link-danger" rel="tooltip" title="Remove" onclick="deleteKeyGoal(' . $keygoal->id . ')"><i class="bi bi-trash"></i></a>';
                }
                $list .= '</li>';
            }
            $list .= '</ol>';

            return response()->json(['type' => 'success', 'goal' => $list], 200);
        } else {
            return response()->json(['type' => 'success', 'goal' => ''], 422);
        }
    }

    public function destroy(Request $request)
    {
        $flag = $this->performanceReviewKeyGoal->destroy($request->keyGoalId);
        if ($flag) {
            return response()->json(['type' => 'success', 'message' => 'Key goal deleted.'], 200);
        } else {
            return response()->json(['type' => 'error', 'message' => 'Key goal could not be deleted.'], 422);
        }
    }

    public function editOne(Request $request, $id)
    {
        $keyGoal = $this->performanceReviewKeyGoal->find($id);
        return view('PerformanceReview::KeyGoalsReview.edit')
            ->withKeyGoal($keyGoal);
    }

    public function updateOne(Request $request, $id)
    {
        $inputs = $request->validate([
            'title' => 'required'
        ]);
        $keyGoal = $this->performanceReviewKeyGoal->update($id, $inputs);
        if ($keyGoal) {
            return response()->json([
                'type' => 'success',
                'message' => 'Key Goal updated successfully.'
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Key Goal could not be updated.'
        ], 422);
    }

    public function getNeyKeyGoals($performanceId)
    {
        $performanceReview = $this->performanceReview->find($performanceId);
        return response()->json([
            'type' => 'success',
            'keyGoals' => $performanceReview->keyGoals()->where('type', 'current')->get()
        ], 200);
    }

    public function saveDraft(Request $request, $id)
    {
        $performanceReview = $this->performanceReview->findOrFail($id);

        //  Validate input
        $request->validate([
            'keygoals' => 'required|array|min:1',
            'keygoals.*.title' => 'required|string|max:255',
            'keygoals.*.output_deliverables' => 'required|string|max:255',

            'devplans' => 'required|array|min:1',
            'devplans.*.plan' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            //  Delete old dev plans (group E)
            $performanceReview->answers()
                ->whereHas('performanceReviewQuestion', fn($q) => $q->where('group', 'E'))
                ->delete();

            foreach ($request->devplans as $item) {
                $question = $this->performanceReviewQuestion
                    ->where('group', 'E')
                    ->latest('position')
                    ->first();

                if ($question) {
                    PerformanceReviewAnswer::create([
                        'performance_review_id' => $performanceReview->id,
                        'question_id' => $question->id,
                        'answer' => trim($item['plan']),
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            //  Delete old key goals
            $this->performanceReviewKeyGoal
                ->where('performance_review_id', '=', $performanceReview->id)
                ->where('type', 'current')
                ->delete();

            foreach ($request->keygoals as $item) {
                $this->performanceReviewKeyGoal->create([
                    'performance_review_id' => $performanceReview->id,
                    'title' => trim($item['title']),
                    'output_deliverables' => trim($item['output_deliverables']),
                    'type' => 'current',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Saved successfully',
            ]);

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'message' => 'Failed to save',
            ], 500);
        }
    }
}

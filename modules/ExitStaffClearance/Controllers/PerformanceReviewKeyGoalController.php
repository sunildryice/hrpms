<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Illuminate\Http\Request;
use Modules\ExitStaffClearance\Repositories\PerformanceReviewKeyGoalRepository;

class PerformanceReviewKeyGoalController extends Controller
{
    public function __construct(
        StaffClearanceRepository $staffClearance,
        PerformanceReviewKeyGoalRepository $staffClearanceKeyGoal
    )
    {
        $this->staffClearance = $staffClearance;
        $this->staffClearanceKeyGoal = $staffClearanceKeyGoal;
    }

    public function store(Request $request)
    {
        $inputs = array(
            'staff_clearance_id'     => $request->staff_clearance_id,
            'title'                     => $request->title,
            'description_employee'      => $request->description_employee,
            'description_supervisor'    => $request->description_supervisor,
            'type'                      => $request->type,
        );

        $staffClearanceKeyGoal = $this->staffClearanceKeyGoal->create($inputs);

        if ($staffClearanceKeyGoal) {
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

        $staffClearanceKeyGoal = $this->staffClearanceKeyGoal->update($request->keyGoalId, $inputs);

        if ($staffClearanceKeyGoal) {
            return response()->json(['type' => 'success', 'message' => 'Key Goal updated.'], 200);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Key Goal could not be updated.'], 422);
        }
    }

    public function append(Request $request)
    {
        $inputs = array(
            'staff_clearance_id' => $request->staff_clearance_id,
            'type'  => 'current'
        );

        $staffClearanceKeyGoal = $this->staffClearanceKeyGoal->create($inputs);

        if ($staffClearanceKeyGoal) {
            $html = '<tr>';
            $html .= '<td>';
            $html .= '<input style="width: 100%" type="text" name="keygoal_title_'.$staffClearanceKeyGoal->id.'" id="keygoal_title_'.$staffClearanceKeyGoal->id.'">';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<input style="width: 100%" type="text" name="keygoal_employee_'.$staffClearanceKeyGoal->id.'" id="keygoal_employee_'.$staffClearanceKeyGoal->id.'">';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<input disabled style="width: 100%" type="text" name="keygoal_supervisor_'.$staffClearanceKeyGoal->id.'" id="keygoal_supervisor_'.$staffClearanceKeyGoal->id.'">';
            $html .= '</td>';
            $html .= '<td class="text-center">';
            $html .= '<a role="button" onclick="removeGoal(event,'.$staffClearanceKeyGoal->id.')" class="link-danger" rel="tooltip" title="Remove"><i class="bi bi-trash"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';

            return response()->json(['type' => 'success', 'html' => $html], 200);
        } else {
            return response()->json(['type' => 'success', 'html' => ''], 422);
        }
    }

    public function update(Request $request)
    {
        $inputs = array();

        if ($request->title != '') {
            $inputs['title'] = $request->title;
        }

        if ($request->description_employee != '') {
            $inputs['description_employee'] = $request->description_employee;
        }

        if ($request->description_supervisor != '') {
            $inputs['description_supervisor']   = $request->description_supervisor;
        }

        if ($request->description_employee_annual != '') {
            $inputs['description_employee_annual'] = $request->description_employee_annual;
        }

        if ($request->description_supervisor_annual != '') {
            $inputs['description_supervisor_annual']   = $request->description_supervisor_annual;
        }

        if ($request->type != '') {
            $inputs['type'] = $request->type;
        }

        $staffClearanceKeyGoal = $this->staffClearanceKeyGoal->find($request->key_goal_id);

        if ($staffClearanceKeyGoal) {
            $staffClearanceKeyGoal = $this->staffClearanceKeyGoal->update($request->key_goal_id, $inputs);
        } else {
            $inputs['staff_clearance_id'] = $request->staff_clearance_id;
            $staffClearanceKeyGoal = $this->staffClearanceKeyGoal->create($inputs);
        }


        if ($staffClearanceKeyGoal) {
            return response()->json(['type' => 'success', 'message' => 'Key Goal Saved.'], 200);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Key Goal could not be Saved.'], 422);
        }
    }

    public function getKeyGoalsEmployee(Request $request)
    {
        $staffClearance = $this->staffClearance->find($request->staff_clearance_id);

        $staffClearanceKeyGoals = $this->staffClearanceKeyGoal
                                        ->where('staff_clearance_id', '=', $request->staff_clearance_id)
                                        ->where('created_by', '=', $staffClearance->employee->user->id)
                                        ->where('type', '=', 'future')
                                        ->get();

        if ($staffClearanceKeyGoals) {
            $list = '<ol>';
            foreach($staffClearanceKeyGoals as $keygoal) {
                $list .= '<li>'.$keygoal->title;
                if ($keygoal->created_by == auth()->user()->id) {
                    $list .= '&emsp;<button role="button" class="btn btn-sm open-edit-modal-form" rel="tooltip" title="Edit" href="';
                    $list .= route('performance.keygoal.editOne',$keygoal->id).'" ><i class="bi bi-pencil-square"></i></button>';
                    $list .= '&emsp;<a role="button" class="link-danger" rel="tooltip" title="Remove" onclick="deleteKeyGoal('.$keygoal->id.')"><i class="bi bi-trash"></i></a>';
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
        $staffClearance = $this->staffClearance->find($request->staff_clearance_id);

        $staffClearanceKeyGoals = $this->staffClearanceKeyGoal
                                        ->where('staff_clearance_id', '=', $request->staff_clearance_id)
                                        ->where('type', '=', 'future')
                                        ->where('created_by', '!=', $staffClearance->employee->user->id)
                                        ->get();

        if ($staffClearanceKeyGoals) {
            $list = '<ol>';
            foreach($staffClearanceKeyGoals as $keygoal) {
                $list .= '<li>'.$keygoal->title;
                if ($keygoal->created_by == auth()->user()->id) {
                    $list .= '&emsp;<button role="button" class="btn btn-sm open-edit-modal-form" rel="tooltip" title="Edit" href="';
                    $list .= route('performance.keygoal.editOne',$keygoal->id).'" ><i class="bi bi-pencil-square"></i></button>';
                    $list .= '&emsp;<a role="button" class="link-danger" rel="tooltip" title="Remove" onclick="deleteKeyGoal('.$keygoal->id.')"><i class="bi bi-trash"></i></a>';
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
        $staffClearance = $this->staffClearance->find($request->staff_clearance_id);

        $staffClearanceKeyGoals = $this->staffClearanceKeyGoal
                                        ->where('staff_clearance_id', '=', $request->staff_clearance_id)
                                        ->where('created_by', '=', $staffClearance->employee->user->id)
                                        ->where('type', '=', 'current')
                                        ->get();

        if ($staffClearanceKeyGoals) {
            $list = '<ol>';
            foreach($staffClearanceKeyGoals as $keygoal) {
                $list .= '<li>';
                $list .= '<span id="keygoal-title">'.$keygoal->title.'</span>';
                if ($keygoal->created_by == auth()->user()->id) {
                    $list .= '&emsp;<a role="button" class="link-primary" rel="tooltip" title="Edit" onclick="editKeyGoal(event, '.$keygoal->id.')"><i class="bi bi-pencil-square"></i></i></a>';
                    $list .= '&emsp;<a role="button" class="link-danger" rel="tooltip" title="Remove" onclick="deleteKeyGoal('.$keygoal->id.')"><i class="bi bi-trash"></i></a>';
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
        $flag = $this->staffClearanceKeyGoal->destroy($request->keyGoalId);
        if ($flag) {
            return response()->json(['type' => 'success', 'message' => 'Key goal deleted.'], 200);
        } else {
            return response()->json(['type' => 'error', 'message' => 'Key goal could not be deleted.'], 422);
        }
    }

    public function editOne(Request $request, $id)
    {
        $keyGoal = $this->staffClearanceKeyGoal->find($id);
        return view('ExitStaffClearance::KeyGoalsReview.edit')
                    ->withKeyGoal($keyGoal);
    }

    public function updateOne(Request $request, $id)
    {
        $inputs = $request->validate([
            'title' => 'required'
        ]);
        $keyGoal = $this->staffClearanceKeyGoal->update($id, $inputs);
        if($keyGoal){
            return response()->json([
                'type' => 'success',
                'message' => 'Key Goal updated successfully.'
            ],200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Key Goal could not be updated.'
        ],422);
    }

    public function getNeyKeyGoals($performanceId)
    {
        $staffClearance = $this->staffClearance->find($performanceId);
        return response()->json([
            'type'=> 'success',
            'keyGoals' => $staffClearance->keyGoals()->where('type', 'current')->get()
        ], 200);
    }
}

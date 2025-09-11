<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ExitStaffClearance\Models\StaffClearanceDepartment;
use Modules\ExitStaffClearance\Notifications\StaffClearanceRecordsFilled;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRecordRepository;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Yajra\DataTables\Facades\DataTables;

class StaffClearanceRecordController extends Controller
{
    public function __construct(
        protected StaffClearanceRepository $staffClearance,
        protected StaffClearanceDepartment $clearanceDepartments,
        protected StaffClearanceRecordRepository $staffClearanceRecords
    ) {
    }

    public function index(Request $request, $clearanceId)
    {
        if ($request->ajax()) {
            $data = $this->staffClearanceRecords->with(['clearedBy', 'clearanceDepartment'])
                ->where('staff_clearance_id', $clearanceId)
                ->orderBy('clearance_department_id', 'asc');

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('department', function ($row) {
                    return ' <span class="fw-bold">'.$row->clearanceDepartment->parent?->title.':</span> '.$row->clearanceDepartment->title;
                })->addColumn('cleared_by', function ($row) {
                    return $row->getClearedByName();
                })->addColumn('cleared_date', function ($row) {
                    return $row->cleared_at?->format('Y-m-d');
                })->addColumn('action', function ($row) {
                    $btn = '';

                    return $btn;
                })
                ->rawColumns(['department', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request, $clearance)
    {
        $clearance = $this->staffClearance->find($clearance);
        $inputs = $request->validate([
            'clearance' => 'array',
            'clearance.*' => 'array',
            'clearance.*.check' => 'nullable',
            'clearance.*.remarks' => 'nullable',
        ]);
        $inputs = array_map(function ($value) {
            return array_filter($value, function ($v) {
                return array_filter($v);
            });
        }, $inputs);
        $inputs['created_by'] = auth()->id();
        $inputs['cleared_at'] = now();
        $flag = $this->staffClearance->updateOrCreateRecords($clearance->id, $inputs);

        if ($flag) {

            if ($clearance->recordsFilled()) {
                $clearance->employee->supervisor->user->notify(new StaffClearanceRecordsFilled($clearance));
            }

            return response()->json(['type' => 'success', 'message' => 'Clearance Record Saved.'], 200);
        } else {
            return response()->json(['type' => 'success', 'message' => 'Clearance Record could not be saved.'], 422);
        }
    }

    public function storeMany(Request $request)
    {
        $staffClearanceId = $request->staff_clearance_id;
        $answersData = $request->data;

        foreach ($answersData as $answerData) {
            $questionId = $answerData['clearance_department_id'];
            $answer = $answerData['answer'];

            $staffClearanceRecords = $this->staffClearanceRecords
                ->where('staff_clearance_id', '=', $staffClearanceId)
                ->where('clearance_department_id', '=', $questionId)
                ->first();

            $inputs = [
                'staff_clearance_id' => $staffClearanceId,
                'clearance_department_id' => $questionId,
                'answer' => $answer,
            ];

            if ($staffClearanceRecords) {
                $staffClearanceRecords->update($inputs);
            } else {
                $this->staffClearanceRecords->create($inputs);
            }
        }

        $savedAnswer = $this->staffClearanceRecords->where('staff_clearance_id', '=', $staffClearanceId)
            ->where('answer', '=', 'true')
            ->first();

        return response()->json(['type' => 'success', 'message' => 'Answers saved.',
            'question' => $savedAnswer->performanceReviewQuestion->question,
        ], 200);
    }

    public function get(Request $request)
    {
        $staffClearanceRecords = $this->staffClearanceRecords
            ->where('staff_clearance_id', '=', $request->staff_clearance_id)
            ->where('clearance_department_id', '=', $request->clearance_department_id)
            ->first();
        $answer = '';
        if ($staffClearanceRecords) {
            $answer = $staffClearanceRecords->answer;

            return response()->json(['type' => 'success', 'answer' => $answer], 200);
        } else {
            return response()->json(['type' => 'success', 'answer' => $answer], 422);
        }
    }

    public function destroy($id)
    {
        $record = $this->staffClearanceRecords->find($id);
        $flag = $this->staffClearanceRecords->destroy($record->id);
        if ($flag) {
            return response()->json(['type' => 'success', 'message' => 'Record successfully deleted.', 'clearanceDepartmentId' => $record->clearance_department_id], 200);
        } else {
            return response()->json(['type' => 'error', 'message' => 'Record could not be deleted'], 422);
        }
    }
}

<?php

namespace Modules\EmployeeAttendance\Requests\AttendanceDonor;

use Illuminate\Validation\Rule;
use Modules\EmployeeAttendance\Requests\AttendanceDetail\StoreRequest as RequestsStoreRequest;

class StoreRequest extends RequestsStoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'log_date' => ['required',
                'date',
                Rule::unique('work_plan_daily_logs')
                    ->where('work_plan_id', $this->worklog)
                    ->where('donor_id', $this->donor),
            ],
            'activities' => 'required',
            'project_id' => 'required|exists:lkup_project_codes,id',

            'attendanceDate' => 'required',
            'checkInTime' => 'nullable',
            'checkOutTime' => 'nullable',
            'donorId' => 'nullable',
            'charged_hours' => 'nullable',
        ];
    }
}

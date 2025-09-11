<?php

namespace Modules\EmployeeAttendance\Requests\AttendanceDetail;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'activities' => 'nullable',
            'project_id' => 'sometimes|nullable|exists:lkup_project_codes,id',

            'attendanceId' => 'required',
            'attendanceDate' => 'required',
            'checkInTime' => 'nullable',
            'checkOutTime' => 'nullable',
            'donorId' => 'nullable',
            'chargedHours' => [
                'nullable',
                'regex:/^\d+(\.\d{1,2})?$/',
                function ($attribute, $value, $fail) {
                    if (strpos($value, '.') !== false) {
                        [$hours, $minutes] = explode('.', $value);
                        if ((int) $minutes >= 60) {
                            $fail('Invalid hours format: Minutes should be less than 60.');
                        }
                    }
                },
                'gte:0',
            ],
        ];
    }
}

<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Department;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\TravelType;
use Modules\Master\Models\ProjectCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office_id',
        'department_id',
        'travel_type_id',
        'project_code_id',
        'requester_id',
        'employee_id',
        'reviewer_id',
        'approver_id',
        'fiscal_year_id',
        'prefix',
        'travel_number',
        'modification_number',
        'modification_travel_request_id',
        'departure_date',
        'return_date',
        'request_date',
        'final_destination',
        'purpose_of_travel',
        'remarks',
        'external_travelers',
        'external_traveler_count',

        //advance
        'requested_advance_amount',
        'advance_requested_at',
        'received_advance_amount',
        'advance_received_at',
        'finance_user_id',
        'finance_remarks',
        'requester_advance_remarks',

        'status_id',
        'cancelled_at',
        'cancel_remarks',
        'substitute_id',
        'created_by',
        'updated_by',
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['departure_date', 'return_date', 'request_date', 'advance_requested_at', 'advance_received_at'];

    protected $casts = [
        'external_travelers' => 'array',
    ];
    /**
     * Get the accompanying staff for travel request.
     */
    public function accompanyingStaffs()
    {
        return $this->belongsToMany(Employee::class, 'travel_request_accompaniments');
    }

    public function financeUser()
    {
        return $this->belongsTo(User::class, 'finance_user_id')->withDefault();
    }

    /**
     * Get the approved log for the travel request.
     */
    public function approvedLog()
    {
        return $this->hasOne(TravelRequestLog::class, 'travel_request_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver of travel request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get modified child request of a travel request
     */
    public function childTravelRequest()
    {
        return $this->hasOne(TravelRequest::class, 'modification_travel_request_id');
    }

    /**
     * Get the department of the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalyear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the logs for the travel request.
     */
    public function logs()
    {
        return $this->hasMany(TravelRequestLog::class, 'travel_request_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get parent of the modified travel request.
     */
    public function parentTravelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'modification_travel_request_id');
    }

    /**
     * Get the project code of travel request.
     */
    public function projectCode()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }
     /**
     * Get the project code of travel request from PMS Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_code_id')->withDefault();
    }

    /**
     * Get the requester
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function getTraveller()
    {
        return $this->isConsultantTravel() ? $this->employee : $this->requester->employee;
    }

    public function getTravellerName()
    {
        return $this->getTraveller()->getFullName();
    }

    public function getTravellerDesignation()
    {
        return $this->getTraveller()->getDesignationName();
    }

    public function getTravellerAddress()
    {
        return $this->getTraveller()->address->getPermanentAddress();
    }

    public function getTravellerDepartment()
    {
        return $this->getTraveller()->getDepartmentName();
    }

    public function getTravellerDutyStation()
    {
        return $this->getTraveller()->getDutyStation();
    }

    public function getTravellerPhone()
    {
        return $this->getTraveller()->mobile_number;
    }

    /**
     * Get the recommended log for the travel request.
     */
    public function recommendedLog()
    {
        return $this->hasOne(TravelRequestLog::class, 'travel_request_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get the reviewer of travel request
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the travel status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the submitted log for the travel request.
     */
    public function submittedLog()
    {
        return $this->hasOne(TravelRequestLog::class, 'travel_request_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get all substitutes of travel request.
     */
    public function substitutes()
    {
        return $this->belongsToMany(Employee::class, 'travel_request_substitutes', 'travel_request_id', 'substitute_id');
    }

    /**
     * Get the travel request claim of the travel request
     */
    public function travelClaim()
    {
        return $this->hasOne(TravelClaim::class, 'travel_request_id');
    }

    /**
     * Get the travel report of the travel request
     */
    public function travelReport()
    {
        return $this->hasOne(TravelReport::class, 'travel_request_id');
    }

    /**
     * Get the travel request estimate
     */
    public function travelRequestEstimate()
    {
        return $this->hasOne(TravelRequestEstimate::class, 'travel_request_id');
    }

    /**
     * Get the data for the travel request itinerary.
     */
    public function travelRequestItineraries()
    {
        return $this->hasMany(TravelRequestItinerary::class, 'travel_request_id')
            ->orderBy('departure_date');
    }

    // In TravelRequest.php
    public function travelRequestDayItineraries()
    {
        return $this->hasMany(TravelRequestDayItinerary::class, 'travel_request_id')
            ->orderBy('date', 'asc');
    }

     public function airTicketItineraries()
    {
        return $this->travelRequestDayItineraries()->where('air_ticket', true);
    }

    /**
     * Get the travel type of the travel request.
     */
    public function travelType()
    {
        return $this->belongsTo(TravelType::class, 'travel_type_id')->withDefault();
    }

    public function getAccompanyingStaffs()
    {
        return $this->accompanyingStaffs ? implode(', ', $this->accompanyingStaffs->pluck('full_name')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->received_advance_amount);
    }

    public function formattedRequestedAmount()
    {
        return number_format($this->requested_advance_amount);
    }

    public function getAdvanceRequestDate()
    {
        return $this->advance_requested_at?->format('d M, Y');
    }

    public function getAdvanceReceivedDate()
    {
        return $this->advance_received_at?->format('d M, Y');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getRequesterDesignation()
    {
        return $this->requester->employee?->getDesignationName() ?? '';
    }

    public function getReportDate()
    {
        return $this->travelReport?->created_at?->format('d M Y')
            ?? $this->travelReport?->updated_at?->format('d M Y')
            ?? now()->format('d M Y');
    }

    public function getFinanceUserName()
    {
        return $this->financeUser->getFullName();
    }


    public function getDepartureDate()
    {
        return $this->departure_date ? $this->departure_date->toFormattedDateString() : '';
    }

    public function getProjectCode()
    {
        // return $this->projectCode->getProjectCodeWithDescription();
        return $this->project->getProjectNameWithShortName();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getReturnDate()
    {
        return $this->return_date ? $this->return_date->toFormattedDateString() : '';
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getSubstitutes()
    {
        return $this->substitutes ? implode(', ', $this->substitutes->pluck('full_name')->map(function ($item) {
            return ucwords($item);
        })->toArray()) : '';
    }

    /**
     * Get the total days of the travel.
     */
    public function getTotalDays()
    {
        return $this->return_date ? $this->return_date->diffInDays($this->departure_date) + 1 : 1;
    }

    public function getTravelRequestNumber()
    {
        $travelNumber = $this->prefix . '-' . $this->travel_number;
        $travelNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';

        return $travelNumber . $fiscalYear;
    }

    public function getTravelType()
    {
        return $this->travelType->title;
    }

    public function getTravelMode()
    {
        $mode_of_travel = '';
        $records = $this->travelRequestItineraries;
        foreach ($records as $record) {
            if ($record->getTravelModes() != '') {
                $mode_of_travel .= $mode_of_travel == '' ? $record->getTravelModes() : ',' . $record->getTravelModes();
            }
        }

        return $mode_of_travel;
    }

    public function getIsAmended()
    {
        return $this->parentTravelRequest ? 'Yes' : 'No';
    }

    public function getIsApproved()
    {
        return $this->status_id == config('constant.APPROVED_STATUS') ? 'Yes' : 'No';
    }

    public function isConsultantTravel()
    {
        return $this->requester->employee_id != $this->employee_id && isset($this->employee_id);
    }

    public function getExternalTravelersAttribute($value)
    {
        if (empty($value) || $value === 'null') {
            return [];
        }
        $current = $value;
        while (
            is_string($current) &&
            str_starts_with($current, '"') &&
            str_ends_with($current, '"') &&
            substr_count($current, '"') >= 2
        ) {
            $current = json_decode($current, true);
        }

        if (is_string($current)) {
            $decoded = json_decode($current, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        if (is_array($current)) {
            return $current;
        }

        return [];
    }

    public function setExternalTravelersAttribute($value)
    {
        if (empty($value) || (is_array($value) && empty(array_filter($value, fn($i) => !empty($i['name'] ?? null))))) {
            $this->attributes['external_travelers'] = null;
            return;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }
        $clean = array_values(
            array_filter((array) $value, fn($item) => !empty($item['name'] ?? null))
        );

        $this->attributes['external_travelers'] = $clean ? json_encode($clean) : null;
    }
}

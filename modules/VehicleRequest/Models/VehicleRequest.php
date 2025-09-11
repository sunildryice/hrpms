<?php

namespace Modules\VehicleRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\District;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Vehicle;
use Modules\Master\Models\VehicleRequestType;
use Modules\Master\Models\VehicleType;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class VehicleRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vehicle_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicle_request_type_id',
        'fiscal_year_id',
        'office_id',
        'prefix',
        'vehicle_request_number',
        'modification_number',
        'modification_vehicle_request_id',
        'purpose_of_travel',
        'employee_ids',
        'remarks',
        'start_datetime',
        'end_datetime',
        'vehicle_type_ids',
        'other_remarks',
        'for_hours_flag',
        'for_hours',
        'for_hours_other_remarks',
        'pickup_time',
        'pickup_place',
        'travel_from',
        'destination',
        'end_time',
        'number_overnight_stay',
        'extra_travel',
        'tentative_cost',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'district_ids',
        'assigned_vehicle_id',
        'assigned_departure_datetime',
        'assigned_arrival_datetime',
        'assigned_remarks',
        'status_id',
        'close_remarks',
        'closed_at',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['start_datetime', 'end_datetime'];

    /**
     * Get the accountCode of the vehicle request.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the vehicle request.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the approver of the vehicle request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the assignedVehicle of the vehicle request.
     */
    public function assignedVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id')->withDefault();
    }

    /**
     * Get modified child request of a vehicle request
     */
    public function childChildRequest()
    {
        return $this->hasOne(VehicleRequest::class, 'modification_vehicle_request_id');
    }

    /**
     * Get the donorCode of the vehicle request.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get Procurement Officers of Hire Vehicle Request
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function procurementOfficers()
    {
        return $this->belongsToMany(User::class, 'vehicle_request_procurements', 'vehicle_request_id', 'officer_id');
    }

    /**
     * Get logs of the vehicle request.
     */
    public function logs()
    {
        return $this->hasMany(VehicleRequestLog::class, 'vehicle_request_id');
    }

    /**
     * Get the office of the vehicle.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get parent of the modified vehicle request.
     */
    public function parentVehicleRequest()
    {
        return $this->belongsTo(VehicleRequest::class, 'modification_vehicle_request_id');
    }

    /**
     * Get requester of the vehicle request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the reviewer of the vehicle request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the vehicle request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the vehicle request type of the vehicle request.
     */
    public function vehicleRequestType()
    {
        return $this->belongsTo(VehicleRequestType::class, 'vehicle_request_type_id')->withDefault();
    }

    public function closedLog()
    {
        return $this->hasOne(VehicleRequestLog::class, 'vehicle_request_id')->where('status_id', config('constant.CLOSED_STATUS'))->latest();
    }

    public function getClosedByName()
    {
        return $this->closedLog?->createdBy->getFullName();
    }

    public function getAccompanyingStaffs()
    {
        $return = '';
        $employeeIds = json_decode($this->employee_ids);
        foreach ($employeeIds as $index => $employeeId) {
            $return .= Employee::find($employeeId)->getFullName();
            $return .= $index == count($employeeIds) - 1 ? '' : ', ';
        }
        return $return;
    }

    public function latestReturnLog()
    {
        return $this->hasOne(VehicleRequestLog::class, 'vehicle_request_id')->where('status_id', config('constant.RETURNED_STATUS'))->latest();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getAssignedVehicleNumber()
    {
        return $this->assignedVehicle->vehicle_number;
    }

    public function getDifferenceInDays()
    {
        return ($this->end_datetime && $this->start_datetime) ? $this->end_datetime->diffInDays($this->start_datetime) + 1 : 1;
    }

    public function getDistricts()
    {
        $districts = District::find(json_decode($this->district_ids));
        return $districts->pluck('district_name')->implode(',');
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getEndDatetime()
    {
        return $this->end_datetime?->format('j M, Y g:i A');
    }

    public function getOfficeName()
    {
        return $this->office->office_name;
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getStartDatetime()
    {
        return $this->start_datetime?->format('j M, Y g:i A');
    }

    public function getVehicleRequestNumber()
    {
        $vehicleRequestNumber = $this->prefix . '-' . $this->vehicle_request_number;
        $vehicleRequestNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2): '';
        return $vehicleRequestNumber . $fiscalYear;
    }

    public function getVehicleRequestType()
    {
        return $this->vehicleRequestType->title;
    }

    public function getVehicleTypes()
    {
        return  implode(', ',array_map(function($id){
            if ($id == -1) {
                return $this->other_remarks ?? 'Other';
            } else {
                $vehicleType = VehicleType::select('title')->find($id);
                return  $vehicleType ? $vehicleType->getVehicleType() : '';
            }
        },json_decode($this->vehicle_type_ids)));
    }

    public function getRequestSubmissionDate()
    {
        return $this->logs->where('status_id', config('constant.SUBMITTED_STATUS'))->last()?->created_at?->toFormattedDateString();
    }

    public function getRequestApprovalDate()
    {
        return $this->logs->where('status_id', config('constant.APPROVED_STATUS'))->last()?->created_at?->toFormattedDateString();
    }

    public function getRequestAssignDate()
    {
        return $this->logs->where('status_id', config('constant.ASSIGNED_STATUS'))->last()?->created_at?->toFormattedDateString();
    }

    public function getRequestRecommendedDate()
    {
        return $this->logs->where('status_id', config('constant.RECOMMENDED_STATUS'))->last()?->created_at?->toFormattedDateString();
    }

    public function getOvernights()
    {
        return ($this->end_datetime && $this->start_datetime) ? $this->end_datetime->diffInDays($this->start_datetime) : 0;
    }

    public function getFor()
    {
        switch ($this->for_hours_flag) {
            case 1:
                $forText = 'Full day';
                break;
            case 2:
                $forText = 'Half day';
                break;
            case 3:
                $forText = $this->for_hours . ' Hours';
                break;
            case 4:
                $forText = $this->for_hours_other_remarks;
                break;
            default:
                $forText = '';
        }
        return $forText;
    }
}

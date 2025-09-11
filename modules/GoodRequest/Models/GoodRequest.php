<?php

namespace Modules\GoodRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class GoodRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'good_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'project_code_id',
        'prefix',
        'good_request_number',
        'purpose',
        'receiver_note',
        'receiver_id',
        'received_at',
        'reviewer_id',
        'approver_id',
        'logistic_officer_id',
        'handover_date',
        'status_id',
        'is_direct_dispatch',
        'is_direct_assign',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['received_at', 'handover_date'];

    /**
     * Get the approver of a good
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the items for the good request.
     */
    public function goodRequestItems()
    {
        return $this->hasMany(GoodRequestItem::class, 'good_request_id');
    }

    public function goodRequestAssets()
    {
        return $this->hasMany(GoodRequestAsset::class, 'good_request_id');
    }

    public function latestGoodRequestItem()
    {
        return $this->hasOne(GoodRequestItem::class, 'good_request_id')->latestOfMany();
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'good_request_employees', 'good_request_id', 'employee_id');
    }

    public function recipients()
    {
        return $this->hasMany(GoodRequestRecipient::class, 'good_request_id');
    }

    /**
     * Get the logs for the good request.
     */
    public function logs()
    {
        return $this->hasMany(GoodRequestLog::class, 'good_request_id')
            ->orderBy('created_at', 'desc');
    }

    public function approvedLog()
    {
        return $this->hasOne(GoodRequestLog::class, 'good_request_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->withDefault()
            ->latest();
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the project of the good request.
     */
    public function projectCode()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }

    /**
     * Get requester of the good request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->withDefault();
    }

    public function getReceiverName()
    {
        return $this->receiver->getFullName();
    }

    /**
     * Get reviewer of the good request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function logisticOfficer()
    {
        return $this->belongsTo(User::class, 'logistic_officer_id')->withDefault();
    }

    /**
     * Get the good request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getLogisticOfficerName()
    {
        return $this->logisticOfficer->getFullName();
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->title;
    }

    public function getGoodRequestNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->prefix.'-'.$this->good_request_number.$fiscalYear;
    }

    public function getProjectCode()
    {
        return $this->projectCode->getProjectCodeWithDescription();
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }
}

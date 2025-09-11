<?php

namespace Modules\ConstructionTrack\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;
use Modules\ConstructionTrack\Models\Construction;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Master\Models\TransactionType;

class ConstructionInstallment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'construction_installments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'construction_id',
        'advance_release_date',
        'transaction_type_id',
        'installment_number',
        'amount',
        'remarks',
        'status_id',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'advance_release_date'
    ];


    /**
     * Get the approver of a construction
     */
    public function construction()
    {
        return $this->belongsTo(Construction::class, 'construction_id')->withDefault();
    }

    /**
     * Get the district of the employee.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id')->withDefault();
    }

       public function getDistrictName()
    {
        return $this->district->district_name;
    }


    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the advance status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the logs for the advance request.
     */
    public function logs()
    {
        return $this->hasMany(ConstructionInstallmentLog::class, 'construction_installment_id');
    }

    /**
     * Get the approved log for the advance request.
     */
    public function approvedLog()
    {
        return $this->hasOne(ConstructionInstallmentLog::class, 'construction_installment_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }


    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    //  /**
    //  * Get the advance items for the advance request.
    //  */
    // public function advanceRequestDetails()
    // {
    //     return $this->hasMany(AdvanceRequestDetail::class, 'advance_request_id');
    // }

    // public function getEstimatedAmount()
    // {
    //     return $this->advanceRequestDetails->sum('amount');
    // }

    // public function getAdvanceRequestNumber()
    // {
    //     return $this->prefix . $this->advance_number;
    // }

    // public function getRequestDate()
    // {
    //     return $this->request_date->toFormattedDateString();
    // }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    // public function getRequiredDate()
    // {
    //     return $this->required_date->toFormattedDateString();
    // }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getAdvanceReleaseDate()
    {
        return $this->advance_release_date->toFormattedDateString();
    }

    public function getLatestRemark()
    {
        return $this->logs->last();
    }

    public function getRequester()
    {
        return $this->requester->getFullName();
    }

    public function getReviewer()
    {
        return $this->reviewer->getFullName();
    }

    public function getApprover()
    {
        return $this->approver->getFullName();
    }


}

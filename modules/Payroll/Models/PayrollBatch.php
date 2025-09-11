<?php

namespace Modules\Payroll\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class PayrollBatch extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payroll_batches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payroll_fiscal_year_id',
        'fiscal_year_id',
        'employee_type_id',
        'month',
        'posted_date',
        'approved_date',
        'description',
        'status_id',
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

    protected $dates = ['posted_date', 'approved_date'];

    /**
     * Get the approver of payroll batch
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the created by user of payroll batch
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the fiscal year of payroll batch
     */
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the logs for the payroll batch.
     */
    public function logs()
    {
        return $this->hasMany(PayrollBatchLog::class, 'payroll_batch_id');
    }

    /**
     * Get the payroll fiscal year of payroll batch
     */
    public function payrollFiscalYear()
    {
        return $this->belongsTo(PayrollFiscalYear::class, 'payroll_fiscal_year_id')->withDefault();
    }

    /**
     * Get the review-er of payroll batch
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the payroll sheets for the payroll batch.
     */
    public function sheets()
    {
        return $this->hasMany(PayrollSheet::class, 'payroll_batch_id');
    }

    /**
     * Get payroll sheet details for the payroll batch.
     */
    public function sheetDetails()
    {
        return $this->hasManyThrough(PayrollSheetDetail::class, PayrollSheet::class);
    }

    /**
     * Get the payroll batch status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the updated by user of payroll batch
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getApprovedDate()
    {
        return $this->approved_date ?->toFormattedDateString();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->getFiscalYear();
    }

    public function getMonth()
    {
        return \DateTime::createFromFormat('!m', $this->month)->format('F');
    }

    public function getPostedDate()
    {
        return $this->posted_date ?->toFormattedDateString();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }
}

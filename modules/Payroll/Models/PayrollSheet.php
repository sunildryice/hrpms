<?php

namespace Modules\Payroll\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class PayrollSheet extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payroll_sheets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payroll_batch_id',
        'employee_id',
        'department_id',
        'designation_id',
        'start_date',
        'end_date',
        'married',
        'disabled',
        'remote_category',
        'gross_amount',
        'total_deduction_amount',
        'sst_amount',
        'tax_liability',
        'tax_discount_amount',
        'tax_amount',
        'tds_amount',
        'net_amount',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the createdBy of the sheet.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Get the payroll sheet details for the payroll sheet.
     */
    public function details()
    {
        return $this->hasMany(PayrollSheetDetail::class, 'payroll_sheet_id');
    }

    /**
     * Get the employee of the sheet.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the payroll batch of the sheet.
     */
    public function payrollBatch()
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getDisabledStatus()
    {
        return $this->disabled ? 'Yes' : 'No';
    }

    public function getGrossAmount()
    {
        return number_format($this->gross_amount, 2);
    }

    public function getMarriedStatus()
    {
        return $this->married ? 'Yes' : 'No';
    }

    public function getNetAmount()
    {
        return number_format($this->net_amount, 2);
    }

    public function getTaxAmount()
    {
        return number_format($this->tax_amount, 2);
    }

    public function getTdsAmount()
    {
        return number_format($this->tds_amount, 2);
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }
}

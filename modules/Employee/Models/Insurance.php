<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Payroll\Models\PayrollFiscalYear;

class Insurance extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_insurance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'payroll_fiscal_year_id',
        'insurer',
        'amount',
        'paid_date',
        'attachment',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['paid_date'];

    /**
     * Get the employee of the address.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the employee of the address.
     */
    public function payrollFiscalYear()
    {
        return $this->belongsTo(PayrollFiscalYear::class, 'payroll_fiscal_year_id');
    }

    public function getPayrollFiscalYear()
    {
        return $this->payrollFiscalYear ? $this->payrollFiscalYear->title : '';
    }

    public function getPaidDate()
    {
        return $this->paid_date ? $this->paid_date->toFormattedDateString() : '';
    }
}

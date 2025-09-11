<?php

namespace Modules\Payroll\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_payroll_tax_rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payroll_fiscal_year_id',
        'married',
        'annual_income_from',
        'annual_income_to',
        'tax_rate',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function fiscalYear()
    {
        return $this->belongsTo(PayrollFiscalYear::class, 'payroll_fiscal_year_id')->withDefault();
    }

    /**
     * get fiscal year
     */
    public function getFiscalYear()
    {
        return $this->fiscalYear->getFiscalYear();
    }
}

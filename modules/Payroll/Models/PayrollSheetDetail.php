<?php

namespace Modules\Payroll\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Status;
use Modules\PaymentSheet\Models\PaymentSheet;
use Modules\Privilege\Models\User;

class PayrollSheetDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payroll_sheet_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payroll_sheet_id',
        'payment_item_id',
        'amount',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the payroll sheet of the sheet detail.
     */
    public function payrollSheet()
    {
        return $this->belongsTo(PayrollSheet::class, 'payroll_sheet_id');
    }

    /**
     * Get the payment item of the sheet detail.
     */
    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class, 'payment_item_id');
    }

    public function getAmount()
    {
        return number_format($this->amount, 2);
    }
}

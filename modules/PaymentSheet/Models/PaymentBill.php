<?php

namespace Modules\PaymentSheet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\BillCategory;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Supplier\Models\Supplier;

class PaymentBill extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_bills';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'supplier_id',
        'office_id',
        'category_id',
        'bill_number',
        'bill_date',
        'remarks',
        'vat_flag',
        'bill_amount',
        'vat_amount',
        'total_amount',
        'paid_percentage',
        'settled_amount',
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

    protected $dates = ['bill_date'];

    /**
     * Get the category of the payment bill.
     */
    public function category()
    {
        return $this->belongsTo(BillCategory::class, 'category_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the office of the payment bill.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function paymentSheetDetails()
    {
        return $this->hasMany(PaymentSheetDetail::class, 'payment_bill_id');
    }

    /**
     * Get requester of the payment bill.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the payment sheet supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault();
    }

    public function getBillDate()
    {
        return $this->bill_date ? $this->bill_date->toFormattedDateString() : "";
    }

    public function getBillNumber()
    {
        return $this->bill_number;
    }

    public function getCategoryName()
    {
        return $this->category ?->title;
    }

    public function getCreatedBy()
    {
        return $this->requester->getFullName();
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->title;
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getSupplierName()
    {
        return $this->supplier->getSupplierName();
    }

    public function getSupplierVatPanNumber()
    {
        return $this->supplier->vat_pan_number;
    }
}

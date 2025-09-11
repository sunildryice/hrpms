<?php

namespace Modules\PaymentSheet\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Office;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;

class PaymentSheetDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_sheet_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_sheet_id',
        'payment_bill_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'processed_by_office_id',
        'charged_office_id',
        'percentage',
        'total_amount',
        'vat_percentage',
        'tds_percentage',
        'vat_amount',
        'amount_with_vat',
        'tds_amount',
        'net_amount',
        'description',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the accountCode of the purchase request item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the purchase request item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    public function chargedOffice()
    {
        return $this->belongsTo(Office::class, 'charged_office_id')->withDefault();
    }

    /**
     * Get the donorCode of the purchase request item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the payment bill of the payment sheet detail.
     */
    public function paymentBill()
    {
        return $this->belongsTo(PaymentBill::class, 'payment_bill_id')->withDefault();
    }

    /**
     * Get the payment sheet of the payment sheet detail.
     */
    public function paymentSheet()
    {
        return $this->belongsTo(PaymentSheet::class, 'payment_sheet_id');
    }

    public function processedByOffice()
    {
        return $this->belongsTo(Office::class, 'processed_by_office_id')->withDefault();
    }

    public function purchaseOrderItems()
    {
        return $this->belongsToMany(PurchaseOrderItem::class, 'po_item_ps_detail', 'ps_detail_id', 'po_item_id');
    }

    public function getBillNumber()
    {
        return $this->paymentBill->getBillNumber();
    }

    public function getBillDate()
    {
        return $this->paymentBill->getBillDate();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getChargedOffice()
    {
        return $this->chargedOffice->getOfficeCode();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getProcessedByOffice()
    {
        return $this->processedByOffice->getOfficeCode();
    }

    public function getDescription()
    {
        return $this->description;
    }
}

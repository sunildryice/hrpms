<?php

namespace Modules\PaymentSheet\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseOrder\Models\PurchaseOrderItem;
use Modules\Supplier\Models\Supplier;

class PaymentSheet extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_sheets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'supplier_id',
        'project_code_id',
        'office_id',
        'prefix',
        'sheet_number',
        'district_id',
        'total_amount',
        'vat_amount',
        'tds_amount',
        'net_amount',
        'deduction_amount',
        'paid_amount',
        'deduction_remarks',
        'voucher_reference_number',
        'purpose',
        'is_from_po',
        'verifier_id',
        'reviewer_id',
        'recommender_id',
        'approver_id',
        'status_id',
        'pay_date',
        'paid_at',
        'payment_remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['bill_date', 'pay_date'];

    /**
     * Get the approver of a fund
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the district of the expense.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
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

    /**
     * Get requester of the payment sheet.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get requester of the payment sheet.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the payment sheet status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the payment sheet supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault();
    }

    /**
     * Get the logs for the payment sheet.
     */
    public function logs()
    {
        return $this->hasMany(PaymentSheetLog::class, 'payment_sheet_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the payment sheet details for the payment sheet.
     */
    public function paymentSheetDetails()
    {
        return $this->hasMany(PaymentSheetDetail::class, 'payment_sheet_id');
    }

    /**
     * Get the purchase orders for the payment sheet.
     */
    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'payment_sheet_purchase_orders', 'payment_sheet_id', 'purchase_order_id');
    }

    /**
     * Get verifier.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getBillDate()
    {
        return $this->bill_date ? $this->bill_date->toFormattedDateString() : '';
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->title;
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

    public function getPaymentSheetNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2) : '';

        return $this->prefix.'-'.$this->sheet_number.$fiscalYear;
    }

    public function getSupplierName()
    {
        return $this->supplier->getSupplierName();
    }

    public function getSupplierVatPanNumber()
    {
        return $this->supplier->vat_pan_number;
    }

    public function getPaymentBillNumber()
    {
        return $this->paymentSheetDetails->map(function ($paymentSheetDetail) {
            return $paymentSheetDetail->getBillNumber();
        })->implode(', ');
    }

    public function getPaymentBillDate()
    {
        return $this->paymentSheetDetails->map(function ($paymentSheetDetail) {
            return $paymentSheetDetail->getBillDate();
        })->implode(', ');
    }

    public function getPaymentDate()
    {
        return $this->pay_date->toFormattedDateString();
    }

    public function getPaymentOffice()
    {
        return $this->paymentSheetDetails->map(function ($paymentSheetDetail) {
            return $paymentSheetDetail->office->getOfficeName();
        })->implode(', ');
    }

    public function getApprovedDate()
    {
        $log = $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->whereStatusId(6)->latest()->first();

        return isset($log) ? $log->created_at->format('M d, Y') : '';
    }

    public function getSubmittedDate()
    {
        $log = $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->whereStatusId(3)->latest()->first();

        return isset($log) ? $log->created_at->format('M d, Y') : '';
    }

    public function getRecommendedDate()
    {
        $log = $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')
            ->where(function ($query) {
                $query->where('status_id', config('constant.RECOMMENDED_STATUS'))
                    ->orWhere('status_id', config('constant.RECOMMENDED2_STATUS'));
            })
            ->where('user_id', $this->recommender->id)
            ->latest()->first();

        return isset($log) ? $log->created_at->format('M d, Y') : '';
    }

    public function getVerifiedDate()
    {
        $log = $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->whereStatusId(config('constant.VERIFIED_STATUS'))->latest()->first();

        return isset($log) ? $log->created_at->format('M d, Y') : '';
    }

    public function getReviewedDate()
    {
        $log = $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')
            ->whereStatusId(config('constant.RECOMMENDED2_STATUS'))->latest()->first();

        return isset($log) ? $log->created_at->format('M d, Y') : '';
    }

    public function verifiedLog()
    {
        return $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->where('status_id', config('constant.VERIFIED_STATUS'))->latest();
    }

    public function reviewerLog()
    {
        return $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->where('status_id', config('constant.RECOMMENDED2_STATUS'))->latest();
    }

    public function submittedLog()
    {
        return $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->whereStatusId(config('constant.SUBMITTED_STATUS'))->latest();
    }

    public function paidLog()
    {
        return $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->latest();
    }

    public function approvedLog()
    {
        return $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->whereStatusId(config('constant.APPROVED_STATUS'))->latest();
    }

    public function submittedDate()
    {
        return $this->submittedLog?->created_at?->format('M d, Y');
    }

    public function paidDate()
    {
        return $this->pay_date?->format('M d, Y');
    }

    public function approvedDate()
    {
        return $this->approvedLog?->created_at?->format('M d, Y');
    }

    public function recommendedLog()
    {
        return $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')->where('status_id', config('constant.RECOMMENDED_STATUS'))->latest()->withDefault();
    }

    public function getVerifierName()
    {
        return $this->verifier->getFullName();
    }

    public function getTotalAmountWithVat()
    {
        return number_format($this->paymentSheetDetails->reduce(function ($carry, $item) {
            return $carry += $item->amount_with_vat;
        }), 2, '.', '');
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getPaymentStatus()
    {
        return $this->status_id == config('constant.PAID_STATUS') ? 'Paid' : 'Due';
    }

    public function getPurpose()
    {
        return $this->purpose;
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    public function getRecommender()
    {
        $recommendedLog = $this->logs->last(function ($log) {
            return $log->status_id == config('constant.RECOMMENDED_STATUS');
        });

        if ($recommendedLog != null) {
            $recommender = (object) collect([
                'name' => $recommendedLog?->createdBy?->getFullName(),
                'designation' => $recommendedLog?->createdBy?->employee?->latestTenure?->getDesignationName(),
                'recommended_date' => $recommendedLog?->created_at->toFormattedDateString(),
            ])->all();

            return $recommender;
        }

        return null;
    }

    public function getPayerName()
    {
        return $this->paidLog()?->first()->createdBy->getFullName();
    }

    public function getDistricts()
    {
        return $this->paymentSheetDetails()->select(['id', 'payment_sheet_id', 'charged_office_id'])
            ->get()->map(function ($detail) {
                $detail->district_name = $detail->chargedOffice->getDistrictName();

                return $detail;
            })->implode('district_name', ', ');
    }

    public function getPurchaseOrderItems()
    {
        $poIds = explode(', ', $this->paymentSheetDetails()->select(['id', 'payment_sheet_id', 'po_item_ids'])
            ->get()->implode('po_item_ids', ', '));

        return PurchaseOrderItem::whereIn('id', $poIds)->get();
    }

    public function getVerifierDesignation()
    {
        return $this->verifiedLog->getDesignation() ?: $this->verifier->employee->designation->title;
    }

    public function getPayerDesignation()
    {
        return $this->paidLog->getDesignation() ?: $this->paidLog->createdBy->employee->designation->title;
    }

    public function getApproverDesignation()
    {
        return $this->approvedLog->getDesignation() ?: $this->approver->employee->designation->title;
    }

    public function getRecommenderDesignation()
    {
        $log = $this->hasOne(PaymentSheetLog::class, 'payment_sheet_id')
            ->where(function ($query) {
                $query->where('status_id', config('constant.RECOMMENDED_STATUS'))
                    ->orWhere('status_id', config('constant.RECOMMENDED2_STATUS'));
            })
            ->where('user_id', $this->recommender->id)
            ->latest()->first();

        return isset($log) ? $log->getDesignation() : '';
    }

    public function getReviewerDesignation()
    {
        return $this->reviewerLog?->getDesignation() ?: $this->reviewer?->employee->designation->title;
    }
}

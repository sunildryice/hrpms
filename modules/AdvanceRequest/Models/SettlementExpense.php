<?php

namespace Modules\AdvanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\DonorCode;

class SettlementExpense extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'advance_settlement_expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advance_settlement_id',
        'advance_request_detail_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'district_id',
        'narration',
        'location',
        'gross_amount',
        'tax_amount',
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
     * Get the advance settlement of the advance settlement expense.
     */
    public function advanceSettlement()
    {
        return $this->belongsTo(Settlement::class, 'advance_settlement_id');
    }

    public function advanceRequestDetail()
    {
        return $this->belongsTo(AdvanceRequestDetail::class, 'advance_request_detail_id')->withDefault();
    }

    /**
     * Get the accountCode of the purchase order item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the purchase order item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the details of the expense.
     */
    public function details()
    {
        return $this->hasMany(SettlementExpenseDetail::class, 'settlement_expense_id');
    }

    /**
     * Get the district of the expense.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    /**
     * Get the donorCode of the purchase order item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }
}

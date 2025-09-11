<?php

namespace Modules\Lta\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Lta\Models\LtaContract;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;

class LtaContractItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lta_contract_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lta_contract_id',
        'item_id',
        'unit_id',
        'specification',
        'quantity',
        'discount_amount',
        'unit_price',
        'total_price',
        'vat_amount',
        'tds_amount',
        'total_amount',
        'account_code_id',
        'activity_code_id',
        'donor_code_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [];

    public function ltaContract()
    {
        return $this->belongsTo(LtaContract::class, 'lta_contract_id')->withDefault();
    }

    /**
     * Get the item of the lta contract item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the unit of the lta contarct item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
    }

    /**
     * Get the accountCode of the lta item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activityCode of the lta contract item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the donorCode of the lta contract item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getDeliveryDate()
    {
        return $this->delivery_date->toFormattedDateString();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    public function getItemName()
    {
        return $this->item->getItemName();
    }

    public function getUnitName()
    {
        return $this->unit->getUnitName();
    }

    public function getLtaContractNumber()
    {
        $ltaContract = $this->ltaContract;
        if ($ltaContract) {
            return $ltaContract->contract_number;
            // return $ltaContract->prefix . '-' . $ltaContract->contract_number;
        }
        return '';
    }

}

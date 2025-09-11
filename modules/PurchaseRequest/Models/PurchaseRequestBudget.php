<?php

namespace Modules\PurchaseRequest\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\District;
use Modules\Master\Models\Office;
use Modules\Privilege\Models\User;

class PurchaseRequestBudget extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_request_budgets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_request_id',
        'district_id',
        'office_id',
        'activity_code_id',
        'balance_budget',
        'commitment_amount',
        'estimated_balance_budget',
        'budgeted',
        'description',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }
    
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getDistrict()
    {
        return $this->district->getDistrictName();
    }

    public function getOffice()
    {
        return $this->office->getOfficeName();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function isBudgeted()
    {
        return (bool)$this->is_budgeted ? 1 : 0;
    }
}

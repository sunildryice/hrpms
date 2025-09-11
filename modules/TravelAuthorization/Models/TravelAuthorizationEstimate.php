<?php

namespace Modules\TravelAuthorization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\TravelAuthorization\Models\TravelAuthorization;

class TravelAuthorizationEstimate extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_authorization_estimates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_authorization_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'particulars',
        'quantity',
        'days',
        'unit_price',
        'total_price',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['departure_date', 'arrival_date'];

    /**
     * Get the account code of travel request.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the activity code of travel request.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the donor code of travel request.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the office of the employee.
     */
    public function travelAuthorization()
    {
        return $this->belongsTo(TravelAuthorization::class, 'travel_authorization_id');
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }
}


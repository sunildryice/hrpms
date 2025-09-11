<?php

namespace Modules\TravelRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\TravelMode;
use Modules\Master\Models\TravelModes;

class LocalTravelItinerary extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'local_travel_reimbursement_itineraries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'local_travel_reimbursement_id',
        'travel_mode_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'travel_mode',
        'travel_date',
        'purpose',
        'departure_place',
        'arrival_place',
        'total_distance',
        'total_fare',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['travel_date'];

    /**
     * Get the activity code.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the account code.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the donor code.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the local travel of the local travel itinerary.
     */
    public function localTravel()
    {
        return $this->belongsTo(TravelRequest::class, 'local_travel_reimbursement_id');
    }

    /**
     * Get the travel mode of the local travel itinerary.
     */
    public function travelMode()
    {
        return $this->belongsTo(TravelMode::class, 'travel_mode_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCode();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCode();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCode();
    }

    public function getDonorDesc()
    {
        return $this->donorCode->getDescription();
    }

    public function getTravelDate()
    {
        return $this->travel_date->toFormattedDateString();
    }

    public function getTravelMode()
    {
        return $this->travel_mode;
    }
}

<?php

namespace Modules\TravelRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\DsaCategory;
use Modules\Master\Models\Office;
use Modules\Master\Models\TravelMode;
use Modules\TravelRequest\Models\TravelRequest;

class TravelRequestItinerary extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_request_itineraries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_request_id',
        'activity_code_id',
        'account_code_id',
        'donor_code_id',
        'dsa_category_id',
        'departure_date',
        'arrival_date',
        'departure_place',
        'arrival_place',
        'dsa_unit_price',
        'dsa_total_price',
        'charging_office_id',
        'description',
        'travel_mode',
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
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    /**
     * Get the travel mode of the travel request itinerary.
     */
    public function travelModes()
    {
        return $this->belongsToMany(TravelMode::class, 'travel_itinerary_modes', 'travel_request_itinerary_id', 'travel_mode_id');
    }

    /**
     * Get the dsa category of the travel request itinerary.
     */
    public function dsaCategory()
    {
        return $this->belongsTo(DsaCategory::class, 'dsa_category_id')->withDefault();
    }

    public function chargingOffice()
    {
        return $this->belongsTo(Office::class, 'charging_office_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getArrivalDate()
    {
        return $this->arrival_date ? $this->arrival_date->toFormattedDateString() : '';
    }

    public function getDepartureDate()
    {
        return $this->departure_date ? $this->departure_date->toFormattedDateString() : '';
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

    /**
     * Get the overnights of the travel itinerary.
     */
    public function getOvernights()
    {
        $overnight = 0;
        if($this->arrival_date && $this->departure_date){
            if($this->arrival_date == $this->travelRequest->return_date){
                $overnight = $this->arrival_date->diffInDays($this->departure_date);
            } else {
                $overnight = $this->arrival_date->diffInDays($this->departure_date)+1;
            }
        }
        return $overnight;
    }

    public function getDsaCategory()
    {
        return $this->dsaCategory->title;
    }

    public function getTravelModes()
    {
        if ($travelModes = $this->travelModes) {
            return $travelModes->pluck('title')->filter(function ($title) {
                return strtolower($title) !== 'others';
            })->map(function ($title) {
                return ucwords($title);
            })->push(ucwords($this->travel_mode))->implode(', ');
        }
    }

    /**
     * Get the total days of the travel.
     */
    public function getTotalDays()
    {
        return $this->arrival_date ? $this->arrival_date->diffInDays($this->departure_date) : 1;
    }

}

<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;

use Modules\Master\Models\TravelMode;

use Modules\Master\Models\DsaCategory;
use Illuminate\Database\Eloquent\Model;
use Modules\TravelRequest\Models\TravelRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelClaimItinerary extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_claim_itineraries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_claim_id',
        'travel_itinerary_id',
        'overnights',
        'percentage_charged',
        'total_amount',
        'office_id',
        'attachment',
        'description',
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
     * Get the travel claim of the travel claim itinerary.
     */
    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_claim_id');
    }

    /**
     * Get the travel request itinerary of the travel claim itinerary.
     */
    public function travelRequestItinerary()
    {
        return $this->belongsTo(TravelRequestItinerary::class, 'travel_itinerary_id');
    }

    public function getTravelMode()
    {
        return $this->travelMode->title;
    }

    /**
     * Get the charging office
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function getActivityTitle()
    {
        return $this->travelRequestItinerary->activityCode->title;
    }

    public function getDonorDescription()
    {
        return $this->travelRequestItinerary->donorCode->description;
    }

    public function getAmount()
    {
        return $this->total_amount;
    }
}

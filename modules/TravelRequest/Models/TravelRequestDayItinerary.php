<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\TravelRequest\Models\TravelRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelRequestDayItinerary extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_request_day_itineraries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_request_id',
        'date',
        'planned_activities',
        'accommodation',
        'air_ticket',
        'departure_place',
        'arrival_place',
        'departure_time',
        'vehicle',
        'vehicle_request_form_link',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'accommodation' => 'boolean',
        'air_ticket' => 'boolean',
        'vehicle' => 'boolean',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the parent travel request.
     */
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->date?->format('d M Y');
    }

    /**
     * Get formatted departure time (if exists).
     */
    public function getFormattedDepartureTimeAttribute()
    {
        return $this->departure_time ?? '-';
    }

    /**
     * Get departure location or dash.
     */
    public function getDeparturePlaceDisplayAttribute()
    {
        return $this->air_ticket ? ($this->departure_place ?: '-') : '-';
    }

    /**
     * Get arrival location or dash.
     */
    public function getArrivalPlaceDisplayAttribute()
    {
        return $this->air_ticket ? ($this->arrival_place ?: '-') : '-';
    }

    /**
     * Get vehicle request link display (for future use).
     */
    public function getVehicleRequestLinkDisplayAttribute()
    {
        if (!$this->vehicle) {
            return '-';
        }

        return $this->vehicle_request_form_link
            ? '<a href="' . $this->vehicle_request_form_link . '" target="_blank">View Request</a>'
            : 'Pending';
    }

    /**
     * Check if this day requires any special arrangement (accommodation, air, vehicle).
     */
    public function getHasArrangementsAttribute()
    {
        return $this->accommodation || $this->air_ticket || $this->vehicle;
    }
}
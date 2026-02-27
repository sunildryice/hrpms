<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\TravelMode;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelDsaClaim extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_dsa_claim';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_claim_id',
        'activity_code_id',
        'activities',
        'departure_date',
        'arrival_date',
        'departure_place',
        'arrival_place',
        'days_spent',
        'breakfast',
        'lunch',
        'dinner',
        'incident_cost',
        'total_dsa',
        'daily_allowance',
        'lodging_expense',
        'other_expense',
        'total_amount',
        'remarks',
        'attachment',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $casts = [
        'departure_date' => 'date',
        'arrival_date' => 'date',
    ];

    /**
     * Get the travel claim of the travel claim itinerary.
     */
    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_claim_id');
    }

    /**
     * Get the activityCode of the travel dsa claim.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    public function travelModes()
    {
        return $this->belongsToMany(
            TravelMode::class,
            'travel_dsa_claim_modes',
            'travel_dsa_claim_id',
            'travel_mode_id'
        );
    }

    public function getArrivalDate()
    {
        return $this->arrival_date?->format('d M Y');
    }

    public function getDepartureDate()
    {
        return $this->departure_date?->format('d M Y');
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

    public function getDaySpents()
    {
        $daySpents = 0;
        if ($this->arrival_date && $this->departure_date) {
            if ($this->arrival_date == $this->travelRequest->return_date) {
                $daySpents = $this->arrival_date->diffInDays($this->departure_date);
            } else {
                $daySpents = $this->arrival_date->diffInDays($this->departure_date) + 1;
            }
        }
        return $daySpents;
    }

}

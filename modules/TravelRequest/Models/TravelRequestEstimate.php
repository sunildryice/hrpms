<?php

namespace Modules\TravelRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\TravelRequest\Models\TravelRequest;

class TravelRequestEstimate extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_request_estimates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_request_id',
        'estimated_dsa',
        'estimated_air_fare',
        'estimated_vehicle_fare',
        'estimated_hotel_accommodation',
        'estimated_airport_taxi',
        'miscellaneous_amount',
        'estimated_event_activities_cost',
        'miscellaneous_remarks',
        'total_amount',
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
     * Get the office of the employee.
     */
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function getTotalAmount()
    {
        return $this->estimated_dsa + $this->estimated_air_fare + $this->estimated_vehicle_fare + $this->miscellaneous_amount + $this->estimated_hotel_accommodation + $this->estimated_airport_taxi + $this->estimated_event_activities_cost;
    }
}

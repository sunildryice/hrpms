<?php

namespace Modules\TravelAuthorization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;


class TravelAuthorizationItinerary extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_authorization_itineraries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_authorization_id',
        'travel_date',
        'place_from',
        'place_to',
        'activities',
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

   public function travelAuthorization()
    {
        return $this->belongsTo(TravelAuthorization::class, 'travel_authorization_id');
    }
   }

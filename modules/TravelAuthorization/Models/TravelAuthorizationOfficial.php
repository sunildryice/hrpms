<?php

namespace Modules\TravelAuthorization\Models;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TravelAuthorization\Models\TravelAuthorization;

class TravelAuthorizationOfficial extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_authorization_officials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_authorization_id',
        'name',
        'post',
        'level',
        'office',
        'district_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function travelAuthorization()
    {
        return $this->belongsTo(TravelAuthorization::class, 'travel_authorization_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}

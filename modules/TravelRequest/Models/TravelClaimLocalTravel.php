<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityCode;
use Modules\Project\Models\ProjectActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelClaimLocalTravel extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_claim_local_travel';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_claim_id',
        'activity_code_id',
        'purpose',
        'travel_date',
        'departure_place',
        'arrival_place',
        'travel_fare',
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
        'travel_date' => 'date',
    ];

    /**
     * Get the travel claim of the travel claim local travel.
     */
    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_claim_id');
    }
    /**
     * Get the activityCode of the travel claim local travel.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }
    public function activity()
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_code_id')->withDefault();
    }

    public function getTravelDate()
    {
        return $this->travel_date?->format('d M Y, h:i A');
    }

}

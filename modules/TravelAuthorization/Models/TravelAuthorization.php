<?php

namespace Modules\TravelAuthorization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\TravelAuthorization\Models\TravelAuthorizationEstimate;

class TravelAuthorization extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_authorization_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office_id',
        'requester_id',
        'reviewer_id',
        'recommender_id',
        'approver_id',
        'fiscal_year_id',
        'prefix',
        'ta_number',
        'modification_number',
        'modification_ta_request_id',
        'request_date',
        'objectives',
        'outcomes',
        'remarks',
        'status_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['request_date'];

    /**
     * Get the approved log for the travel request.
     */
    public function approvedLog()
    {
        return $this->hasOne(TravelAuthorizationLog::class, 'travel_authorization_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function returnedLog()
    {
        return $this->hasOne(TravelAuthorizationLog::class, 'travel_authorization_id')
            ->whereStatusId(config('constant.RETURNED_STATUS'))
            ->latest();
    }

    /**
     * Get the approver of travel request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    /**
     * Get modified child request of a travel request
     */
    public function childRequest()
    {
        return $this->hasOne(TravelAuthorization::class, 'modification_ta_request_id');
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalyear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the logs for the travel request.
     */
    public function logs()
    {
        return $this->hasMany(TravelAuthorizationLog::class, 'travel_authorization_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get parent of the modified travel request.
     */
    public function parentRequest()
    {
        return $this->belongsTo(TravelAuthorization::class, 'modification_ta_request_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the requester
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }


    /**
     * Get the reviewer of travel request
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the travel status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the submitted log for the travel request.
     */
    public function submittedLog()
    {
        return $this->hasOne(TravelAuthorizationLog::class, 'travel_authorization_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the recommended log for the travel request.
     */
    public function recommendedLog()
    {
        return $this->hasOne(TravelAuthorizationLog::class, 'travel_authorization_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }
    /**
     * Get the travel request estimate
     */
    public function estimates()
    {
        return $this->hasMany(TravelAuthorizationEstimate::class, 'travel_authorization_id');
    }

    public function latestEstimate()
    {
        return $this->hasOne(TravelAuthorizationEstimate::class, 'travel_authorization_id')
            ->latest()->withDefault();
    }

    /**
     * Get the data for the travel request itinerary.
     */
    public function itineraries()
    {
        return $this->hasMany(TravelAuthorizationItinerary::class, 'travel_authorization_id');
    }

    public function officials()
    {
        return $this->hasMany(TravelAuthorizationOfficial::class, 'travel_authorization_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getRecommenderName()
    {
        return $this->recommender->getFullName();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getTravelAuthorizationNumber()
    {
        $travelNumber = $this->prefix . '-' . $this->ta_number;
        $travelNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';
        return $travelNumber . $fiscalYear;
    }

    public function getIsAmended()
    {
        return $this->parentRequest ? 'Yes' : 'No';
    }

    public function getIsApproved()
    {
        return $this->status_id == config('constant.APPROVED_STATUS') ? 'Yes' : 'No';
    }
}

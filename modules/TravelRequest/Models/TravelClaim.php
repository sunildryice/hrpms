<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelClaim extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_claims';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_request_id',
        'total_expense_amount',
        'total_itinerary_amount',
        'advance_amount',
        'refundable_amount',
        'total_amount',
        'agree_at',
        'reviewer_id',
        'recommender_id',
        'approver_id',
        'status_id',
        'pay_date',
        'paid_at',
        'payment_remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['pay_date', 'paid_at'];
    protected $appends = ['total_local_travel_amount'];

    /**
     * Get the approved log for the travel claim.
     */
    public function approvedLog()
    {
        return $this->hasOne(TravelClaimLog::class, 'travel_claim_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function returnLog()
    {
        return $this->hasOne(TravelClaimLog::class, 'travel_claim_id')
            ->whereStatusId(config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    /**
     * Get the approver of the travel claim
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the attachments of the travel claim
     */
    public function attachments()
    {
        return $this->hasMany(TravelClaimAttachment::class, 'travel_claim_id');
    }

    /**
     * Get the expenses of the travel claim
     */
    public function expenses()
    {
        return $this->hasMany(TravelClaimExpense::class, 'travel_claim_id');
    }

    /**
     * Get the itineraries of the travel claim
     */
    public function dsaClaim()
    {
        return $this->hasMany(TravelDsaClaim::class, 'travel_claim_id');
    }
    /**
     * Get the local travels of the travel claim
     */
    public function localTravels()
    {
        return $this->hasMany(TravelClaimLocalTravel::class, 'travel_claim_id');
    }

    public function getTotalLocalTravelAmountAttribute()
    {
        return $this->localTravels->sum('travel_fare');
    }

    /**
     * Get the logs of the travel claim
     */
    public function logs()
    {
        return $this->hasMany(TravelClaimLog::class, 'travel_claim_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the recommender of the travel claim
     */
    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    /**
     * Get the recommended log for the travel claim.
     */
    public function recommendedLog()
    {
        return $this->hasOne(TravelClaimLog::class, 'travel_claim_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }

    /**
     * Get the requester of the travel claim
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the reviewed log for the travel claim.
     */
    public function reviewedLog()
    {
        return $this->hasOne(TravelClaimLog::class, 'travel_claim_id')
            ->whereStatusId(config('constant.VERIFIED_STATUS'))
            ->latest();
    }

    /**
     * Get the reviewer of the travel claim
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the report status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the submitted log for the travel claim.
     */
    public function submittedLog()
    {
        return $this->hasOne(TravelClaimLog::class, 'travel_claim_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the travel request of the travel claim.
     */
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    public function getApprovedDate()
    {
        return $this->approvedLog?->created_at->toFormattedDateString();
    }

    public function getSubmittedDate()
    {
        return $this->logs->where('status_id', config('constant.SUBMITTED_STATUS'))->last()?->created_at->toFormattedDateString();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getPaymentDate()
    {
        return $this->pay_date->toFormattedDateString();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getRecommenderName()
    {
        return $this->recommender->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function paidLog()
    {
        return $this->hasOne(TravelClaimLog::class, 'travel_claim_id')
            ->whereStatusId(config('constant.PAID_STATUS'))
            ->latest();
    }

    public function getPayerName()
    {
        return $this->paidLog()?->first()->createdBy->getFullName();
    }

    public function getPayerDesignation()
    {
        return $this->paidLog()?->first()->createdBy->employee->designation->title;
    }
}
